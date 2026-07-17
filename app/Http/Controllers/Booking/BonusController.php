<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\UserBonus;
use App\Models\BonusClaim;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon; 

class BonusController extends Controller
{
    /**
     * GET /api/customer/bonuses
     * Lihat semua bonus aktif customer
     */
    public function index()
    {
        $user = Auth::user();
        
        $bonuses = UserBonus::with([
            'bonusRule', 
            'transaction.serviceCategory'  // Eager load relasi ke serviceCategory
        ])
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->where('valid_until', '>=', now())
        ->orderBy('valid_until', 'asc')
        ->get()
        ->map(function ($bonus) {
            // Default source
            $source = 'Bonus dari Admin';
            
            // Cek apakah ada transaction
            if ($bonus->transaction) {
                // Ambil room_type dari transaction
                $roomType = $bonus->transaction->room_type ?? '';
                
                // Ambil service category name dari relasi
                $serviceCategoryName = $bonus->transaction->serviceCategory->name ?? '';
                
                // Format: "Bonus [room_type] [service_category_name]"
                if ($roomType && $serviceCategoryName) {
                    $source = 'Bonus ' . $roomType . ' ' . $serviceCategoryName;
                } elseif ($roomType) {
                    $source = 'Bonus ' . $roomType;
                } elseif ($serviceCategoryName) {
                    $source = 'Bonus ' . $serviceCategoryName;
                } else {
                    $source = 'Bonus Transaksi #' . $bonus->transaction->order_id;
                }
            }
            
            return [
                'id' => $bonus->id,
                'bonus_hours_total' => $bonus->bonus_hours_total,
                'bonus_hours_used' => $bonus->bonus_hours_used,
                'remaining_hours' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                'valid_until' => $bonus->valid_until->format('d M Y'),
                'status' => $bonus->status,
                'notes' => $bonus->notes,
                'source' => $source,
                'last_claim_month' => $bonus->last_claim_month,
                'last_claim_year' => $bonus->last_claim_year,
                'months_activated' => $bonus->months_activated ?? 0,
                // Optional: tambahkan data mentah untuk debugging
                '_debug' => [
                    'room_type' => $roomType ?? null,
                    'service_category' => $serviceCategoryName ?? null
                ]
            ];
        });

        // Hitung statistik bulan ini
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        
        $usedThisMonth = BonusClaim::whereHas('userBonus', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereYear('claim_date', $currentYear)
            ->whereMonth('claim_date', $currentMonth)
            ->sum('hours_used');
        
        $totalRemainingHours = $bonuses->sum('remaining_hours');
        $totalHours = $bonuses->sum('bonus_hours_total');
        $totalUsedHours = $bonuses->sum('bonus_hours_used');

        return response()->json([
            'success' => true,
            'data' => [
                'total_hours' => $totalHours,
                'total_used_hours' => $totalUsedHours,
                'total_remaining_hours' => $totalRemainingHours,
                'bonuses' => $bonuses,
                'current_month' => [
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'month_name' => $now->format('F Y'),
                    'used' => $usedThisMonth,
                    'remaining' => 4 - $usedThisMonth,
                    'quota' => 4
                ]
            ]
        ]);
    }

    /**
     * GET /api/customer/bonus-claims/history
     * Lihat riwayat claim bonus (transactions dengan payment_type = 'bonus')
     */
    public function claimHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Ambil transactions dengan payment_type = 'bonus'
            $bonusClaims = Transaction::where('user_id', $user->id)
                ->where('payment_type', 'bonus')  // Filter khusus bonus claims
                ->whereIn('status', ['settlement', 'pending', 'failed'])
                ->with(['location', 'room'])  // Eager load relasi
                ->orderBy('created_at', 'desc')
                ->paginate(3)
                ->through(function ($transaction) {
                    // CEK DURASI - sesuai dengan field di database Anda
                    $duration = null;
                    $durationType = null;
                    
                    // Prioritas: jam, hari, minggu, bulan, tahun
                    // Field 'jam' adalah integer (yang terisi)
                    if (!empty($transaction->jam) && $transaction->jam > 0) {
                        $duration = $transaction->jam;
                        $durationType = 'jam';
                    }
                    // Field 'hari' adalah varchar, bisa berisi "3 hari" atau integer
                    elseif (!empty($transaction->hari)) {
                        // Coba ekstrak angka dari string jika perlu
                        if (is_numeric($transaction->hari)) {
                            $duration = $transaction->hari;
                        } else {
                            // Mungkin formatnya "3 hari", ambil angka pertamanya
                            preg_match('/(\d+)/', $transaction->hari, $matches);
                            $duration = $matches[1] ?? null;
                        }
                        $durationType = 'hari';
                    }
                    elseif (!empty($transaction->minggu) && $transaction->minggu > 0) {
                        $duration = $transaction->minggu;
                        $durationType = 'minggu';
                    }
                    elseif (!empty($transaction->bulan) && $transaction->bulan > 0) {
                        $duration = $transaction->bulan;
                        $durationType = 'bulan';
                    }
                    elseif (!empty($transaction->tahun) && $transaction->tahun > 0) {
                        $duration = $transaction->tahun;
                        $durationType = 'tahun';
                    }
                    
                    // Ambil room_number dari relasi room
                    $roomNumber = '';
                    if ($transaction->room) {
                        $roomNumber = $transaction->room->room_number ?? 
                                    $transaction->room->name ?? 
                                    $transaction->room->room_name ?? 
                                    '';
                    }
                    
                    // Ambil location name dari relasi location
                    $locationName = '';
                    if ($transaction->location) {
                        $locationName = $transaction->location->name ?? 
                                    $transaction->location->location_name ?? 
                                    $transaction->location->title ?? 
                                    '';
                    }
                    
                    return [
                        'id' => $transaction->id,
                        'order_id' => $transaction->order_id,
                        'location_id' => $transaction->location_id,
                        'room_id' => $transaction->room_id,
                        'room_type' => $transaction->room_type,
                        'duration' => $duration,
                        'duration_type' => $durationType,
                        'duration_display' => $duration ? $duration . ' ' . $durationType : 'Durasi tidak tersedia',
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                        'status' => $transaction->status,
                        'location_name' => $locationName,
                        'room_name' => $roomNumber,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $bonusClaims->items(),
                'meta' => [
                    'current_page' => $bonusClaims->currentPage(),
                    'last_page' => $bonusClaims->lastPage(),
                    'per_page' => $bonusClaims->perPage(),
                    'total' => $bonusClaims->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat claim bonus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/customer/bonuses/{id}
     * Detail bonus tertentu
     */
    public function show($id)
    {
        $bonus = UserBonus::with(['bonusRule', 'transaction', 'claims.meetingTransaction'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'bonus' => [
                    'id' => $bonus->id,
                    'total_hours' => $bonus->bonus_hours_total,
                    'used_hours' => $bonus->bonus_hours_used,
                    'remaining_hours' => $bonus->remaining_hours,
                    'valid_until' => $bonus->valid_until->format('d M Y'),
                    'status' => $bonus->status,
                    'notes' => $bonus->notes
                ],
                'usage_history' => $bonus->claims->map(function ($claim) {
                    return [
                        'date' => $claim->claim_date->format('d M Y'),
                        'start_time' => $claim->claim_start_time,
                        'hours_used' => $claim->hours_used,
                        'room' => $claim->meetingTransaction->room->room_number ?? '-',
                        'status' => $claim->status
                    ];
                })
            ]
        ]);
    }

    /**
     * GET /api/customer/bonuses/usage-history
     * Riwayat penggunaan semua bonus
     */
    public function usageHistory()
    {
        $claims = BonusClaim::with(['userBonus', 'meetingTransaction.room'])
            ->whereHas('userBonus', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }

    /**
     * GET /api/customer/bonuses/monthly-overview
     * Lihat sisa bonus per bulan (12 bulan ke depan)
     */
    public function monthlyOverview()
    {
        try {
            $user = Auth::user();
            $now = now();
            $today = $now->toDateString(); // Hanya tanggal: 2026-02-23
            
            Log::info('========== MONTHLY OVERVIEW ==========');
            Log::info('User: ' . $user->name . ' (ID: ' . $user->id . ')');
            Log::info('Today: ' . $today);
            
            // Ambil semua bonus aktif user - PERBAIKAN: whereDate untuk valid_until
            $bonuses = UserBonus::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereDate('valid_until', '>=', $today)  // ✅ FIX: Bandingkan DATE dengan DATE
                ->whereRaw('bonus_hours_total > bonus_hours_used')
                ->with(['transaction.serviceCategory'])
                ->get();
            
            Log::info('Total bonuses found: ' . $bonuses->count());
            
            // Jika tidak ada bonus, return empty array
            if ($bonuses->isEmpty()) {
                Log::info('No active bonuses found');
                return response()->json([
                    'success' => true,
                    'data' => [
                        'overview' => [],
                        'summary' => [
                            'total_hours_remaining' => 0,
                            'total_months_remaining' => 0,
                            'expiring_soon' => []
                        ]
                    ]
                ]);
            }
            
            // Log detail setiap bonus untuk debugging
            foreach ($bonuses as $bonus) {
                Log::info('Bonus ID: ' . $bonus->id, [
                    'total_hours' => $bonus->bonus_hours_total,
                    'used_hours' => $bonus->bonus_hours_used,
                    'remaining' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                    'valid_until' => $bonus->valid_until,
                    'months_activated' => $bonus->months_activated ?? 0
                ]);
            }
            
            $monthlyOverview = [];
            $totalHoursRemaining = 0;
            $expiringSoon = [];
            
            // Generate untuk 12 bulan ke depan
            for ($i = 0; $i < 12; $i++) {
                $targetDate = $now->copy()->addMonths($i);
                $year = $targetDate->year;
                $month = $targetDate->month;
                $monthName = $targetDate->format('F Y');
                
                $totalQuota = 0;
                $totalUsed = 0;
                $bonusDetails = [];
                
                foreach ($bonuses as $bonus) {
                    // Cek apakah bonus masih berlaku di bulan ini
                    $bonusValidUntil = Carbon::parse($bonus->valid_until);
                    if ($bonusValidUntil >= $targetDate->endOfMonth()) {
                        // Set quota per bulan (4 jam)
                        $monthlyQuota = 4;
                        
                        // Hitung jam yang sudah dipakai di bulan ini
                        $usedInMonth = BonusClaim::where('user_bonus_id', $bonus->id)
                            ->whereYear('claim_date', $year)
                            ->whereMonth('claim_date', $month)
                            ->sum('hours_used');
                        
                        $remainingInMonth = $monthlyQuota - $usedInMonth;
                        
                        // Format source name (sama seperti di index)
                        $source = 'Bonus dari Admin';
                        if ($bonus->transaction) {
                            $roomType = $bonus->transaction->room_type ?? '';
                            $serviceName = $bonus->transaction->serviceCategory->name ?? '';
                            
                            if ($roomType && $serviceName) {
                                $source = 'Bonus ' . $roomType . ' ' . $serviceName;
                            } elseif ($roomType) {
                                $source = 'Bonus ' . $roomType;
                            } elseif ($serviceName) {
                                $source = 'Bonus ' . $serviceName;
                            } else {
                                $source = 'Bonus Transaksi #' . $bonus->transaction->order_id;
                            }
                        }
                        
                        // Hanya tambahkan ke details jika ada quota atau pernah digunakan
                        if ($monthlyQuota > 0) {
                            $bonusDetails[] = [
                                'bonus_id' => $bonus->id,
                                'source' => $source,
                                'quota' => $monthlyQuota,
                                'used' => (int)$usedInMonth,
                                'remaining' => (int)max(0, $remainingInMonth) // Tidak boleh negatif
                            ];
                            
                            $totalQuota += $monthlyQuota;
                            $totalUsed += $usedInMonth;
                        }
                    }
                }
                
                // Hitung total remaining untuk bulan ini
                $totalRemaining = $totalQuota - $totalUsed;
                $totalHoursRemaining += $totalRemaining;
                
                // Catat bonus yang akan expired (dalam 3 bulan ke depan)
                if ($i < 3 && $totalRemaining > 0) {
                    foreach ($bonusDetails as $detail) {
                        if ($detail['remaining'] > 0) {
                            $expiringSoon[] = [
                                'month' => $monthName,
                                'hours' => $detail['remaining'],
                                'source' => $detail['source']
                            ];
                            break; // Ambil satu saja per bulan
                        }
                    }
                }
                
                // Hanya tambahkan ke overview jika:
                // 1. Masih ada sisa quota (totalRemaining > 0) ATAU
                // 2. Ini bulan berjalan ($i == 0) - tetap tampilkan meskipun 0
                if ($totalRemaining > 0 || $i == 0) {
                    $monthlyOverview[] = [
                        'month_name' => $monthName,
                        'is_current' => ($year == $now->year && $month == $now->month),
                        'total_quota' => $totalQuota,
                        'total_used' => $totalUsed,
                        'total_remaining' => $totalRemaining,
                        'percentage_used' => $totalQuota > 0 ? round(($totalUsed / $totalQuota) * 100) : 0,
                        'bonus_details' => $bonusDetails
                    ];
                }
            }
            
            // Hitung total months remaining (bulan yang masih punya sisa jam)
            $totalMonthsRemaining = count(array_filter($monthlyOverview, function($month) {
                return $month['total_remaining'] > 0;
            }));
            
            Log::info('Monthly overview generated', [
                'months' => count($monthlyOverview),
                'months_with_remaining' => $totalMonthsRemaining,
                'total_hours_remaining' => $totalHoursRemaining
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => $monthlyOverview,
                    'summary' => [
                        'total_hours_remaining' => $totalHoursRemaining,
                        'total_months_remaining' => $totalMonthsRemaining,
                        'expiring_soon' => array_slice($expiringSoon, 0, 3) // Maksimal 3 notifikasi
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Monthly overview error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat overview bulanan: ' . $e->getMessage()
            ], 500);
        }
    }
}