<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\UserBonus;
use App\Models\BonusClaim;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                // Optional: tambahkan data mentah untuk debugging
                '_debug' => [
                    'room_type' => $roomType ?? null,
                    'service_category' => $serviceCategoryName ?? null
                ]
            ];
        });
        
        $totalRemainingHours = $bonuses->sum('remaining_hours');
        $totalHours = $bonuses->sum('bonus_hours_total');
        $totalUsedHours = $bonuses->sum('bonus_hours_used');

        return response()->json([
            'success' => true,
            'data' => [
                'total_hours' => $totalHours,
                'total_used_hours' => $totalUsedHours,
                'total_remaining_hours' => $totalRemainingHours,
                'bonuses' => $bonuses
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
}