<?php

namespace App\Listeners;

use App\Events\TransactionSettled;
use App\Models\Contract;
use Illuminate\Support\Facades\Log;

class CreateContractOnSettlement
{
    public function handle(TransactionSettled $event): void
    {
        $transaction = $event->transaction;

        // Hanya proses Virtual Office untuk saat ini
        if ($transaction->room_type !== 'Virtual Office') {
            return;
        }

        // Guard: skip jika kontrak sudah ada
        if ($transaction->contract()->exists()) {
            Log::info('Contract already exists, skipping.', [
                'transaction_id' => $transaction->id,
            ]);
            return;
        }

        // Pastikan invoice sudah ada
        if (!$transaction->invoice) {
            $transaction->load('invoice');
        }

        if (!$transaction->invoice) {
            Log::warning('Cannot create contract: invoice not found.', [
                'transaction_id' => $transaction->id,
            ]);
            return;
        }

        try {
            // Hitung end_date otomatis dari durasi transaksi
            $startDate = now()->toDateString();
            $endDate   = $this->calculateEndDate($transaction);

            Contract::create([
                'transaction_id' => $transaction->id,
                'invoice_id'     => $transaction->invoice->id,
                'type'           => 'Virtual Office',
                'status'         => 'draft',
                'payment_scheme' => $transaction->paket === 'monthly' ? 'monthly' : 'full',
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                // contract_date → diisi admin saat publish/generate PDF
                // contract_number → diisi saat status active
                // file_path → diisi saat PDF digenerate
            ]);

            Log::info('Contract draft created successfully.', [
                'transaction_id' => $transaction->id,
                'invoice_id'     => $transaction->invoice->id,
                'end_date'       => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create contract draft.', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Hitung end_date berdasarkan durasi transaksi
     */
    private function calculateEndDate($transaction): string
    {
        $start = now();

        if (!empty($transaction->tahun) && $transaction->tahun > 0) {
            return $start->addYears($transaction->tahun)->toDateString();
        }

        if (!empty($transaction->bulan) && $transaction->bulan > 0) {
            return $start->addMonths($transaction->bulan)->toDateString();
        }

        if (!empty($transaction->minggu) && $transaction->minggu > 0) {
            return $start->addWeeks($transaction->minggu)->toDateString();
        }

        if (!empty($transaction->hari) && $transaction->hari > 0) {
            return $start->addDays($transaction->hari)->toDateString();
        }

        // Default fallback: 1 tahun
        return $start->addYear()->toDateString();
    }
}