<?php
// app/Services/RoomStatusService.php

namespace App\Services;

use App\Models\Room;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoomStatusService
{
    public function updateAllRoomStatus()
    {
        $now = Carbon::now('Asia/Jakarta');
        
        Log::info('=== START UPDATE EXPIRED BOOKINGS TO AVAILABLE ===', [
            'timestamp' => $now->format('Y-m-d H:i:s')
        ]);
        
        // ✅ Ambil SEMUA transaksi aktif
        $activeTransactions = Transaction::whereIn('status', ['pending', 'settlement'])
            ->with('room')
            ->get();

        $expiredRoomIds = [];
        $activeRoomIds = [];

        foreach ($activeTransactions as $transaction) {
            try {
                $bookingDate = $transaction->booking_date instanceof Carbon 
                    ? $transaction->booking_date->format('Y-m-d') 
                    : $transaction->booking_date;
                
                $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, 'Asia/Jakarta');
                $end = $this->calculateEndTime($transaction, $start);
                
                // ✅ Jika booking masih aktif atau belum dimulai
                if ($now->lessThan($end)) {
                    $activeRoomIds[] = $transaction->room_id;
                    
                    Log::info('🔄 Active/upcoming booking', [
                        'room_id' => $transaction->room_id,
                        'transaction_id' => $transaction->id,
                        'ends_at' => $end->format('Y-m-d H:i:s')
                    ]);
                } 
                // ✅ Jika booking sudah selesai
                else {
                    $expiredRoomIds[] = $transaction->room_id;
                    
                    Log::info('✅ Expired booking detected', [
                        'room_id' => $transaction->room_id,
                        'transaction_id' => $transaction->id,
                        'ended_at' => $end->format('Y-m-d H:i:s')
                    ]);
                }
                
            } catch (\Exception $e) {
                Log::error('❌ Error processing transaction', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // ✅ Update HANYA ruangan yang booking-nya sudah expired
        $expiredRoomIds = array_unique($expiredRoomIds);
        $activeRoomIds = array_unique($activeRoomIds);
        
        // ✅ Ruangan yang boleh di-set available: expired DAN tidak ada booking aktif lain
        $roomsToAvailable = array_diff($expiredRoomIds, $activeRoomIds);
        
        if (!empty($roomsToAvailable)) {
            $updatedCount = Room::whereIn('id', $roomsToAvailable)
                ->update(['status' => 'available']);
            
            Log::info("✅ Updated {$updatedCount} rooms to AVAILABLE", [
                'room_ids' => $roomsToAvailable
            ]);
        } else {
            Log::info('ℹ️ No rooms to update - all bookings still active');
        }

        Log::info('=== UPDATE COMPLETED ===', [
            'active_bookings' => count($activeRoomIds),
            'expired_bookings' => count($expiredRoomIds),
            'rooms_updated' => count($roomsToAvailable)
        ]);
        
        return count($roomsToAvailable);
    }

    public function updateSingleRoomStatus(Room $room)
    {
        $now = Carbon::now('Asia/Jakarta'); // ✅ Pastikan timezone konsisten
        
        Log::info('=== UPDATE ROOM STATUS ===', [
            'room_id' => $room->id,
            'room_number' => $room->room_number,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'total_transactions' => $room->transactions->count()
        ]);

        $activeTransaction = null;
        $todayUpcomingTransaction = null;

        foreach ($room->transactions as $transaction) {
            // ✅ Cek pending DAN settlement
            if (!in_array($transaction->status, ['pending', 'settlement'])) {
                continue;
            }

            $isActive = $this->isTransactionActive($transaction, $now);
            $isTodayUpcoming = $this->isTransactionTodayUpcoming($transaction, $now);
            
            Log::info('📋 Transaction analysis', [
                'transaction_id' => $transaction->id,
                'booking_date' => $transaction->booking_date instanceof Carbon 
                    ? $transaction->booking_date->format('Y-m-d') 
                    : $transaction->booking_date,
                'start_time' => $transaction->start_time,
                'status' => $transaction->status,
                'is_active' => $isActive,
                'is_today_upcoming' => $isTodayUpcoming
            ]);

            // ✅ Priority: occupied > booked
            if ($isActive) {
                $activeTransaction = $transaction;
                break; // ✅ Langsung break jika ada yang active
            } elseif ($isTodayUpcoming) {
                $todayUpcomingTransaction = $transaction;
            }
        }

        // ✅ Decision logic
        if ($activeTransaction) {
            $room->status = 'occupied';
            Log::info('🚨 Status: OCCUPIED', [
                'transaction_id' => $activeTransaction->id
            ]);
        } elseif ($todayUpcomingTransaction) {
            $room->status = 'booked';
            Log::info('📅 Status: BOOKED', [
                'transaction_id' => $todayUpcomingTransaction->id
            ]);
        } else {
            $room->status = 'available';
            Log::info('✅ Status: AVAILABLE');
        }

        $room->save();
    }

    /**
     * ✅ Cek apakah transaksi sedang berlangsung SEKARANG
     * Support booking multi-hari/minggu/bulan
     */
    private function isTransactionActive(Transaction $transaction, Carbon $now): bool
    {
        try {
            $bookingDate = $transaction->booking_date;
            $startTime = $transaction->start_time;
            
            // ✅ Handle Carbon object
            if ($bookingDate instanceof Carbon) {
                $bookingDateString = $bookingDate->format('Y-m-d');
            } else {
                $bookingDateString = $bookingDate;
            }
            
            $startDateTime = Carbon::parse($bookingDateString . ' ' . $startTime, 'Asia/Jakarta');
            $endTime = $this->calculateEndTime($transaction, $startDateTime);
            
            $isActive = $now->between($startDateTime, $endTime);
            
            if ($isActive) {
                Log::info('⏰ Active booking detected', [
                    'transaction_id' => $transaction->id,
                    'start' => $startDateTime->format('Y-m-d H:i:s'),
                    'end' => $endTime->format('Y-m-d H:i:s'),
                    'now' => $now->format('Y-m-d H:i:s'),
                    'duration_hours' => $startDateTime->diffInHours($endTime)
                ]);
            }
            
            return $isActive;
            
        } catch (\Exception $e) {
            Log::error('❌ Error in isTransactionActive', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * ✅ Cek apakah transaksi mulai HARI INI tapi belum dimulai
     * Hanya booking hari ini yang jadi 'booked', booking besok tidak
     */
    private function isTransactionTodayUpcoming(Transaction $transaction, Carbon $now): bool
    {
        try {
            $bookingDate = $transaction->booking_date;
            $startTime = $transaction->start_time;
            
            // ✅ Handle Carbon object
            if ($bookingDate instanceof Carbon) {
                $bookingDateString = $bookingDate->format('Y-m-d');
            } else {
                $bookingDateString = $bookingDate;
            }
            
            $startDateTime = Carbon::parse($bookingDateString . ' ' . $startTime, 'Asia/Jakarta');
            
            // ✅ FIX: Hanya jika booking HARI INI dan belum dimulai
            $isToday = $startDateTime->isToday();
            $isFuture = $startDateTime->greaterThan($now);
            $isTodayUpcoming = $isToday && $isFuture;
            
            if ($isTodayUpcoming) {
                Log::info('📅 Today upcoming booking detected', [
                    'transaction_id' => $transaction->id,
                    'start' => $startDateTime->format('Y-m-d H:i:s'),
                    'now' => $now->format('Y-m-d H:i:s'),
                    'starts_in_minutes' => $now->diffInMinutes($startDateTime, false)
                ]);
            }
            
            return $isTodayUpcoming;
            
        } catch (\Exception $e) {
            Log::error('❌ Error in isTransactionTodayUpcoming', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * ✅ Hitung waktu berakhir booking berdasarkan durasi
     */
    private function calculateEndTime(Transaction $transaction, Carbon $startTime): Carbon
    {
        $end = $startTime->copy();
        
        if ($transaction->tahun > 0) {
            $end->addYears($transaction->tahun);
        } elseif ($transaction->bulan > 0) {
            $end->addMonths($transaction->bulan);
        } elseif ($transaction->minggu > 0) {
            $end->addWeeks($transaction->minggu);
        } elseif ($transaction->hari > 0) {
            $end->addDays($transaction->hari);
        } elseif ($transaction->jam > 0) {
            $end->addHours($transaction->jam);
        } else {
            $end->addHour(); // Default 1 jam
        }
        
        return $end;
    }

    /**
     * ✅ Debug helper untuk troubleshooting
     */
    public function debugProblematicTransactions()
    {
        Log::info('=== 🔍 DEBUGGING ALL TRANSACTIONS ===');
        
        $now = Carbon::now('Asia/Jakarta');
        
        // ✅ Ambil SEMUA transaksi aktif tanpa filter tanggal
        $transactions = Transaction::whereIn('status', ['pending', 'settlement'])
            ->with('room')
            ->get();

        Log::info('Found transactions', ['count' => $transactions->count()]);

        foreach ($transactions as $transaction) {
            try {
                $bookingDate = $transaction->booking_date instanceof Carbon 
                    ? $transaction->booking_date->format('Y-m-d') 
                    : $transaction->booking_date;
                    
                $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, 'Asia/Jakarta');
                $end = $this->calculateEndTime($transaction, $start);
                
                $isActive = $now->between($start, $end);
                $isTodayUpcoming = $start->isToday() && $start->greaterThan($now);
                
                Log::info('📋 Transaction details', [
                    'id' => $transaction->id,
                    'room_id' => $transaction->room_id,
                    'room_number' => $transaction->room->room_number ?? 'N/A',
                    'status' => $transaction->status,
                    'booking_date' => $bookingDate,
                    'start_time' => $transaction->start_time,
                    'start_datetime' => $start->format('Y-m-d H:i:s'),
                    'end_datetime' => $end->format('Y-m-d H:i:s'),
                    'duration_hours' => $start->diffInHours($end),
                    'is_active' => $isActive,
                    'is_today_upcoming' => $isTodayUpcoming,
                    'should_be_status' => $isActive ? 'occupied' : ($isTodayUpcoming ? 'booked' : 'available')
                ]);
                
            } catch (\Exception $e) {
                Log::error('❌ Error processing transaction', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::info('=== 🔍 DEBUG COMPLETED ===');
    }
}