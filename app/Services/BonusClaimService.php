<?php

namespace App\Services;

use App\Models\UserBonus;
use App\Models\Transaction;
use App\Models\BonusClaim;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BonusClaimService
{
    /**
     * Validasi claim untuk monthly bonus
     */
    private function validateMonthlyClaim(UserBonus $userBonus, int $duration): array
    {
        $now = now();
        
        // 1. Hitung total jam yang sudah dipakai BULAN INI
        $usedThisMonth = BonusClaim::where('user_bonus_id', $userBonus->id)
            ->whereYear('claim_date', $now->year)
            ->whereMonth('claim_date', $now->month)
            ->sum('hours_used');
        
        // 2. Hitung total BULAN yang sudah dipakai (distinct month)
        $monthsUsed = BonusClaim::where('user_bonus_id', $userBonus->id)
            ->distinct()
            ->count(DB::raw('CONCAT(YEAR(claim_date), "-", MONTH(claim_date))'));
        
        // 3. Validasi: max 4 jam per bulan
        if (($usedThisMonth + $duration) > 4) {
            $remainingThisMonth = 4 - $usedThisMonth;
            return [
                'valid' => false,
                'message' => "Maximum 4 jam per bulan. Sisa jam bulan ini: {$remainingThisMonth} jam"
            ];
        }
        
        // 4. Validasi: max 12 bulan total
        if ($monthsUsed >= 12 && $usedThisMonth == 0) {
            return [
                'valid' => false,
                'message' => 'Bonus sudah mencapai 12 bulan penggunaan'
            ];
        }
        
        return [
            'valid' => true,
            'used_this_month' => $usedThisMonth,
            'months_used' => $monthsUsed,
            'remaining_this_month' => 4 - ($usedThisMonth + $duration)
        ];
    }

    /**
     * Claim bonus (support both one-time dan monthly)
     */
    public function claimBonus(array $data, int $adminId)
    {
        return DB::transaction(function () use ($data, $adminId) {
            // 1. Validasi bonus dengan LOCK
            $userBonus = UserBonus::where('id', $data['user_bonus_id'])
                ->where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->where('valid_until', '>=', now())
                ->lockForUpdate()
                ->firstOrFail();
            
            // 2. Cek sisa jam total
            $remainingTotal = $userBonus->bonus_hours_total - $userBonus->bonus_hours_used;
            if ($remainingTotal < $data['duration']) {
                throw new \Exception('Sisa bonus total tidak mencukupi');
            }
            
            // 3. CEK BONUS TYPE - Apakah ini dari Virtual Office (monthly)?
            $isMonthlyBonus = $this->isMonthlyBonus($userBonus);
            
            if ($isMonthlyBonus) {
                $validation = $this->validateMonthlyClaim($userBonus, $data['duration']);
                if (!$validation['valid']) {
                    throw new \Exception($validation['message']);
                }
            }
            
            // 4. Ambil data room dengan relasi
            $room = Room::with(['location.city'])->where('id', $data['room_id'])->firstOrFail();
            
            // 5. Cek konflik booking
            $conflict = Transaction::where('room_id', $data['room_id'])
                ->where('booking_date', $data['booking_date'])
                ->where('start_time', '<', $data['start_time'])
                ->whereRaw('ADDTIME(start_time, SEC_TO_TIME(jam * 3600)) > ?', [$data['start_time']])
                ->exists();
                
            if ($conflict) {
                throw new \Exception('Room sudah dibooking di jam tersebut');
            }
            
            // ✅ HITUNG START DATETIME YANG BENAR
            $startDateTime = Carbon::parse($data['booking_date'] . ' ' . $data['start_time']);
            
            // 6. Generate transaction meeting
            $transaction = Transaction::create([
                // ===== USER & BOOKING INFO =====
                'user_id' => $data['user_id'],
                'room_id' => $data['room_id'],
                'room_type' => 'Meeting Room',
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                'jam' => $data['duration'],
                'hari' => 0,
                'minggu' => 0,
                'bulan' => 0,
                'tahun' => 0,
                'paket' => 'hourly',
                'jumlah_orang' => $data['jumlah_orang'] ?? 1,
                
                // ===== LOCATION & CITY INFO =====
                'location_id' => $room->location_id,
                'city_id' => $room->location->city_id ?? null,
                'city' => $room->location->city->name ?? null,
                
                // ===== MITRA INFO =====
                'mitra_id' => $room->location->mitra_id ?? null,
                
                // ===== PAYMENT INFO =====
                'gross_amount' => 0,
                'payment_type' => 'bonus',
                'status' => 'settlement',
                'order_id' => 'BONUS-' . date('Ymd') . '-' . time() . '-' . $data['user_id'],
                
                // ===== CUSTOMER INFO =====
                'nama_lengkap' => $userBonus->user->name ?? 'Customer',
                'email' => $userBonus->user->email ?? 'customer@example.com',
                'phone' => $userBonus->user->phone ?? null,
                
                // ===== TIMESTAMPS =====
                // ✅ UBAH: transaction_time diisi dengan waktu booking yang benar
                'transaction_time' => $startDateTime->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // 7. Update bonus used
            $userBonus->bonus_hours_used += $data['duration'];
            
            if ($isMonthlyBonus) {
                $userBonus->last_claim_month = now()->month;
                $userBonus->last_claim_year = now()->year;
                $userBonus->months_activated = BonusClaim::where('user_bonus_id', $userBonus->id)
                    ->distinct()
                    ->count(DB::raw('CONCAT(YEAR(claim_date), "-", MONTH(claim_date))'));
            }
            
            if ($userBonus->bonus_hours_used >= $userBonus->bonus_hours_total) {
                $userBonus->status = 'fully_used';
            }
            $userBonus->save();
            
            // 8. Create bonus claim log
            $claim = BonusClaim::create([
                'user_bonus_id' => $userBonus->id,
                'meeting_transaction_id' => $transaction->id,
                'admin_id' => $adminId,
                'hours_used' => $data['duration'],
                'claim_date' => $data['booking_date'],
                'claim_start_time' => $data['start_time'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? ($isMonthlyBonus 
                    ? 'Monthly bonus claim - ' . now()->format('F Y')
                    : 'Booking dari klaim bonus')
            ]);
            
            // 9. Return response
            $response = [
                'claim_id' => $claim->id,
                'meeting_transaction_id' => $transaction->id,
                'hours_used' => $data['duration'],
                'remaining_bonus' => $userBonus->bonus_hours_total - $userBonus->bonus_hours_used,
                'room' => $room->room_number,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'],
                // ✅ TAMBAHKAN: Data untuk real-time update di frontend
                'room_update' => [
                    'id' => $room->id,
                    'number' => $room->room_number,
                    'booking' => [
                        'orderId' => $transaction->order_id,
                        'customerName' => $userBonus->user->name ?? 'Customer',
                        'startDateTime' => $startDateTime->toISOString(),
                        'duration' => $data['duration']
                    ]
                ]
            ];
            
            if ($isMonthlyBonus) {
                $response['monthly_info'] = [
                    'claim_month' => now()->format('F Y'),
                    'used_this_month' => $validation['used_this_month'] + $data['duration'],
                    'months_used' => $validation['months_used'] + 1,
                    'months_remaining' => 12 - ($validation['months_used'] + 1)
                ];
            }
            
            return $response;
        });
    }

    /**
     * Deteksi apakah bonus dari Virtual Office (monthly)
     */
    private function isMonthlyBonus(UserBonus $userBonus): bool
    {
        if ($userBonus->bonusRule && $userBonus->bonusRule->room_type_trigger === 'Virtual Office') {
            return true;
        }
        
        if ($userBonus->transaction && $userBonus->transaction->room_type === 'Virtual Office') {
            return true;
        }
        
        if (str_contains($userBonus->notes ?? '', 'Virtual Office')) {
            return true;
        }
        
        if ($userBonus->bonus_hours_total === 48) {
            return true;
        }
        
        return false;
    }

    /**
     * Get customer's monthly bonus status (untuk frontend)
     */
    public function getCustomerMonthlyStatus(int $userId)
    {
        $now = now();
        
        $bonuses = UserBonus::where('user_id', $userId)
            ->where('status', 'active')
            ->where('valid_until', '>=', $now)
            ->get()
            ->filter(function($bonus) {
                return $this->isMonthlyBonus($bonus);
            });
        
        return $bonuses->map(function($bonus) use ($now) {
            $usedThisMonth = BonusClaim::where('user_bonus_id', $bonus->id)
                ->whereYear('claim_date', $now->year)
                ->whereMonth('claim_date', $now->month)
                ->sum('hours_used');
            
            $monthsUsed = BonusClaim::where('user_bonus_id', $bonus->id)
                ->distinct()
                ->count(DB::raw('CONCAT(YEAR(claim_date), "-", MONTH(claim_date))'));
            
            return [
                'bonus_id' => $bonus->id,
                'total_hours_remaining' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                'hours_used_this_month' => $usedThisMonth,
                'hours_remaining_this_month' => max(0, 4 - $usedThisMonth),
                'can_claim_this_month' => $usedThisMonth < 4,
                'months_used' => $monthsUsed,
                'months_remaining' => 12 - $monthsUsed,
                'valid_until' => $bonus->valid_until->format('d M Y')
            ];
        });
    }
}