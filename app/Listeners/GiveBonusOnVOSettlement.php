<?php

namespace App\Listeners;

use App\Models\Transaction;
use App\Models\UserBonus;
use App\Models\BonusRule;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiveBonusOnVOSettlement
{
    public function handle($event): void
    {
        $transaction = $event->transaction;
        
        // Cek jika transaksi VO dan sudah settlement
        if ($transaction->room_type === 'Virtual Office' && $transaction->status === 'settlement') {
            
            // Cek bonus rule yang sesuai
            $bonusRule = BonusRule::where('room_type_trigger', 'Virtual Office')
                ->where('is_active', true)
                ->where('min_gross_amount', '<=', $transaction->gross_amount)
                ->orderBy('min_gross_amount', 'desc')
                ->first();
            
            if ($bonusRule) {
                UserBonus::create([
                    'user_id' => $transaction->user_id,
                    'bonus_rule_id' => $bonusRule->id,
                    'transaction_id' => $transaction->id,
                    'bonus_hours_total' => $bonusRule->bonus_hours,
                    'valid_until' => now()->addDays($bonusRule->valid_days),
                    'notes' => 'Bonus dari pembayaran ' . $bonusRule->name,
                    'created_by' => 1 // system auto
                ]);
            }
        }
    }
}