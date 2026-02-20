<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\UserBonus;
use App\Models\BonusRule;
use App\Mail\TransactionSettledMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    public function updated(Transaction $transaction)
    {
        // Cek jika status berubah menjadi settlement
        if ($transaction->isDirty('status') && $transaction->status === 'settlement') {
            
            Log::info('💰 Transaction settled', [
                'id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'room_type' => $transaction->room_type,
                'gross_amount' => $transaction->gross_amount
            ]);
            
            // 1. Kirim email
            try {
                Mail::to($transaction->user->email)->send(new TransactionSettledMail($transaction));
                Log::info('✅ Email sent to ' . $transaction->user->email);
            } catch (\Exception $e) {
                Log::error('❌ Failed to send email: ' . $e->getMessage());
            }
            
            // 2. Berikan bonus untuk Virtual Office
            $this->giveBonusForVirtualOffice($transaction);
        }
    }
    
    protected function giveBonusForVirtualOffice(Transaction $transaction)
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
                'bonus_id' => $existingBonus->id
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
            'found' => $bonusRule ? 'yes' : 'no',
            'rule_id' => $bonusRule->id ?? null,
            'min_amount' => $bonusRule->min_gross_amount ?? null,
            'bonus_hours' => $bonusRule->bonus_hours ?? null
        ]);
        
        if ($bonusRule) {
            try {
                $bonus = UserBonus::create([
                    'user_id' => $transaction->user_id,
                    'bonus_rule_id' => $bonusRule->id,
                    'transaction_id' => $transaction->id,
                    'bonus_hours_total' => $bonusRule->bonus_hours,
                    'bonus_hours_used' => 0,
                    'valid_until' => now()->addDays($bonusRule->valid_days),
                    'status' => 'active',
                    'notes' => 'Bonus dari transaksi #' . $transaction->order_id,
                    'created_by' => 1 // system
                ]);
                
                Log::info('✅ BONUS CREATED SUCCESSFULLY!', [
                    'bonus_id' => $bonus->id,
                    'user_id' => $transaction->user_id,
                    'hours' => $bonusRule->bonus_hours,
                    'transaction_id' => $transaction->id
                ]);
                
            } catch (\Exception $e) {
                Log::error('❌ Failed to create bonus', [
                    'error' => $e->getMessage(),
                    'transaction_id' => $transaction->id
                ]);
            }
        } else {
            Log::warning('❌ No eligible bonus rule for amount: ' . $transaction->gross_amount);
        }
    }
}