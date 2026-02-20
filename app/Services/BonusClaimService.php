<?php

namespace App\Services;

use App\Models\UserBonus;
use App\Models\Transaction;
use App\Models\BonusClaim;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class BonusClaimService
{
        public function claimBonus(array $data, int $adminId)
    {
        return DB::transaction(function () use ($data, $adminId) {
            // 1. Validasi bonus
            $userBonus = UserBonus::where('id', $data['user_bonus_id'])
                ->where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->where('valid_until', '>=', now())
                ->firstOrFail();
            
            // 2. Cek sisa jam
            $remainingHours = $userBonus->bonus_hours_total - $userBonus->bonus_hours_used;
            if ($remainingHours < $data['duration']) {
                throw new \Exception('Sisa bonus tidak mencukupi');
            }
            
            // 3. Ambil data room dengan relasi location dan city
            $room = Room::with(['location.city'])->where('id', $data['room_id'])->firstOrFail();
            
            // 4. Cek konflik booking
            $conflict = Transaction::where('room_id', $data['room_id'])
                ->where('booking_date', $data['booking_date'])
                ->where('start_time', '<', $data['start_time'])
                ->whereRaw('ADDTIME(start_time, SEC_TO_TIME(jam * 3600)) > ?', [$data['start_time']])
                ->exists();
                
            if ($conflict) {
                throw new \Exception('Room sudah dibooking di jam tersebut');
            }
            
            // 5. Generate transaction meeting dengan data LENGKAP
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
                'paket' => 'hourly', // ✅ FIX: Paket untuk booking per jam
                'jumlah_orang' => $data['jumlah_orang'] ?? 1,
                
                // ===== LOCATION & CITY INFO =====
                'location_id' => $room->location_id, // ✅ FIX: Ambil dari room
                'city_id' => $room->location->city_id ?? null, // ✅ FIX: Ambil dari relasi location -> city
                'city' => $room->location->city->name ?? null, // ✅ Tambahkan nama kota
                
                // ===== MITRA INFO =====
                'mitra_id' => $room->location->mitra_id ?? null, // ✅ Tambahkan mitra_id
                
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
                'transaction_time' => now(), // ✅ FIX: Set transaction_time
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // 6. Update bonus used
            $userBonus->bonus_hours_used += $data['duration'];
            if ($userBonus->bonus_hours_used >= $userBonus->bonus_hours_total) {
                $userBonus->status = 'fully_used';
            }
            $userBonus->save();
            
            // 7. Create bonus claim log
            $claim = BonusClaim::create([
                'user_bonus_id' => $userBonus->id,
                'meeting_transaction_id' => $transaction->id,
                'admin_id' => $adminId,
                'hours_used' => $data['duration'],
                'claim_date' => $data['booking_date'],
                'claim_start_time' => $data['start_time'],
                'status' => 'confirmed',
                'notes' => $data['notes'] ?? 'Booking dari klaim bonus'
            ]);
            
            return [
                'claim_id' => $claim->id,
                'meeting_transaction_id' => $transaction->id,
                'hours_used' => $data['duration'],
                'remaining_bonus' => $userBonus->bonus_hours_total - $userBonus->bonus_hours_used,
                'room' => $room->room_number,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time']
            ];
        });
    }
}