<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\UserBonus;
use App\Models\BonusRule;
use App\Mail\TransactionSettledMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Auto-generate invoice saat transaksi baru dibuat.
     *
     * Menggunakan firstOrCreate agar atomic — mencegah duplikat
     * meskipun ada concurrent request atau race condition.
     *
     * NOTE: storeManual() di InvoiceController menggunakan saveQuietly()
     * sehingga TIDAK men-trigger method ini. Invoice untuk transaksi manual
     * dibuat langsung oleh storeManual() sendiri.
     */
    public function created(Transaction $transaction): void
    {
        try {
            // firstOrCreate: atomic check + create dalam satu operasi
            // Jika invoice untuk transaction_id ini sudah ada → skip (tidak error)
            [$invoice, $wasCreated] = Invoice::firstOrCreate(
                [
                    // Kondisi pencarian — jika ini sudah ada, tidak akan dibuat baru
                    'transaction_id' => $transaction->id,
                ],
                [
                    // Data yang diisi hanya saat CREATE (bukan update)
                    'invoice_number' => Invoice::generateNumber($transaction),
                    'status'         => $transaction->status,
                    'created_by'     => auth()->id() ?? \App\Models\User::where('role', 'admin')->value('id'),
                ]
            );

            if ($wasCreated) {
                Log::info('✅ Invoice auto-generated: ' . $invoice->invoice_number . ' for transaction #' . $transaction->order_id);
            } else {
                Log::info('ℹ️ Invoice already exists for transaction #' . $transaction->order_id . ', skipping auto-generate.');
            }

        } catch (\Exception $e) {
            // Wrapped dalam try-catch agar tidak crash aplikasi
            // Invoice gagal dibuat tidak boleh menghentikan flow transaksi
            Log::error('❌ Failed to auto-generate invoice for transaction #' . $transaction->order_id . ': ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'trace'          => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle perubahan status transaksi.
     * - Kirim email saat status berubah ke 'settlement'
     * - Berikan bonus Virtual Office
     * - Sync status invoice
     */
    public function updated(Transaction $transaction): void
    {
        if ($transaction->isDirty('status') && $transaction->status === 'settlement') {

            Log::info('💰 Transaction settled', [
                'id'           => $transaction->id,
                'user_id'      => $transaction->user_id,
                'room_type'    => $transaction->room_type,
                'gross_amount' => $transaction->gross_amount,
            ]);

            // 1. Kirim email notifikasi
            try {
                Mail::to($transaction->user->email)->send(new TransactionSettledMail($transaction));
                Log::info('✅ Email sent to ' . $transaction->user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send email: ' . $e->getMessage());
            }

            // 2. Berikan bonus untuk Virtual Office
            $this->giveBonusForVirtualOffice($transaction);

            // 3. Sync status invoice jika sudah ada
            $this->syncInvoiceStatus($transaction);

            // 4. Fire event untuk auto-create contract draft
            try {
                $transaction->loadMissing('invoice');
                event(new \App\Events\TransactionSettled($transaction));
                Log::info('✅ TransactionSettled event fired from Observer.', [
                    'transaction_id' => $transaction->id,
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Failed to fire TransactionSettled event: ' . $e->getMessage());
            }

        }
    }

    /**
     * Sync status invoice mengikuti status transaksi saat transaksi di-update.
     */
    protected function syncInvoiceStatus(Transaction $transaction): void
    {
        try {
            $invoice = $transaction->invoice;

            if (!$invoice) {
                Log::warning('⚠️ No invoice found to sync for transaction #' . $transaction->order_id);
                return;
            }

            if ($invoice->status === $transaction->status) {
                return; // Sudah sync, tidak perlu update
            }

            $invoice->update(['status' => $transaction->status]);

            Log::info('🔄 Invoice status synced: ' . $invoice->invoice_number . ' → ' . $transaction->status);

        } catch (\Exception $e) {
            Log::error('❌ Failed to sync invoice status for transaction #' . $transaction->order_id . ': ' . $e->getMessage());
        }
    }

    /**
     * Berikan bonus jam gratis untuk transaksi Virtual Office yang settlement.
     */
    protected function giveBonusForVirtualOffice(Transaction $transaction): void
    {
        // Hanya untuk Virtual Office
        if ($transaction->room_type !== 'Virtual Office') {
            Log::info('⏭️ Not Virtual Office, skipping bonus');
            return;
        }

        // Cek apakah sudah pernah dapat bonus dari transaksi ini
        $existingBonus = UserBonus::where('transaction_id', $transaction->id)->first();
        if ($existingBonus) {
            Log::info('⚠️ Bonus already exists for transaction', [
                'bonus_id' => $existingBonus->id,
            ]);
            return;
        }

        // Cari rule bonus yang sesuai
        $bonusRule = BonusRule::where('room_type_trigger', 'Virtual Office')
            ->where('is_active', true)
            ->where('min_gross_amount', '<=', $transaction->gross_amount)
            ->orderBy('min_gross_amount', 'desc')
            ->first();

        Log::info('🔍 Checking bonus rule', [
            'found'       => $bonusRule ? 'yes' : 'no',
            'rule_id'     => $bonusRule->id ?? null,
            'min_amount'  => $bonusRule->min_gross_amount ?? null,
            'bonus_hours' => $bonusRule->bonus_hours ?? null,
        ]);

        if ($bonusRule) {
            try {
                $bonus = UserBonus::create([
                    'user_id'           => $transaction->user_id,
                    'bonus_rule_id'     => $bonusRule->id,
                    'transaction_id'    => $transaction->id,
                    'bonus_hours_total' => $bonusRule->bonus_hours,
                    'bonus_hours_used'  => 0,
                    'valid_until'       => now()->addDays($bonusRule->valid_days),
                    'status'            => 'active',
                    'notes'             => 'Bonus dari transaksi #' . $transaction->order_id,
                    'created_by'        => 1, // system
                ]);

                Log::info('✅ BONUS CREATED SUCCESSFULLY!', [
                    'bonus_id'       => $bonus->id,
                    'user_id'        => $transaction->user_id,
                    'hours'          => $bonusRule->bonus_hours,
                    'transaction_id' => $transaction->id,
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Failed to create bonus', [
                    'error'          => $e->getMessage(),
                    'transaction_id' => $transaction->id,
                ]);
            }
        } else {
            Log::warning('❌ No eligible bonus rule for amount: ' . $transaction->gross_amount);
        }
    }
}