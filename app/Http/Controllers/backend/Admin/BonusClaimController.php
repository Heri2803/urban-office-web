<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserBonus;
use App\Models\Transaction;
use App\Models\Room;
use App\Models\User;
use App\Models\BonusClaim;
use App\Services\BonusClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BonusClaimController extends Controller
{
    protected $bonusClaimService;

    public function __construct(BonusClaimService $bonusClaimService)
    {
        $this->bonusClaimService = $bonusClaimService;
    }

    /**
     * GET /api/admin/customers/{user_id}/bonuses
     * Lihat bonus customer untuk keperluan klaim
     */
    public function customerBonuses($userId)
    {
        $bonuses = UserBonus::where('user_id', $userId)
            ->where('status', 'active')
            ->where('valid_until', '>=', now())
            ->whereRaw('bonus_hours_total > bonus_hours_used')
            ->get()
            ->map(function ($bonus) {
                return [
                    'id' => $bonus->id,
                    'remaining_hours' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                    'total_hours' => $bonus->bonus_hours_total,
                    'used_hours' => $bonus->bonus_hours_used,
                    'valid_until' => $bonus->valid_until->format('d M Y'),
                    'notes' => $bonus->notes
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $bonuses
        ]);
    }

    /**
     * GET /api/admin/rooms/available
     * List room tersedia berdasarkan tanggal & jam
     */
    public function availableRooms(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1'
        ]);

        // Ambil semua room yang available
        $rooms = Room::where('status', 'available')
            ->where('location_id', $request->location_id ?? 1) // default location
            ->get();

        // Filter room yang tidak conflict dengan booking existing
        $availableRooms = $rooms->filter(function ($room) use ($request) {
            $conflict = Transaction::where('room_id', $room->id)
                ->where('booking_date', $request->booking_date)
                ->where('start_time', '<', $request->start_time)
                ->whereRaw('ADDTIME(start_time, SEC_TO_TIME(jam * 3600)) > ?', [$request->start_time])
                ->exists();
            
            return !$conflict;
        });

        return response()->json([
            'success' => true,
            'data' => $availableRooms->values()
        ]);
    }

    /**
     * POST /api/admin/bonuses/claim
     * Proses klaim bonus & generate transaction
     */
    public function claim(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_bonus_id' => 'required|exists:user_bonuses,id',
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1',
            'jumlah_orang' => 'nullable|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        try {
            $claim = $this->bonusClaimService->claimBonus($validated, Auth::id());
            
            return response()->json([
                'success' => true,
                'message' => 'Bonus claimed successfully, meeting transaction created',
                'data' => $claim
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/admin/bonus-claims
     * Riwayat semua klaim bonus
     */
    public function index()
    {
        $claims = BonusClaim::with(['userBonus.user', 'meetingTransaction.room', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $claims
        ]);
    }

    /**
     * POST /api/admin/bonus-claims/{id}/cancel
     * Batalkan klaim (soft cancel)
     */
    public function cancel($id)
    {
        DB::transaction(function () use ($id) {
            $claim = BonusClaim::with('userBonus')->findOrFail($id);
            
            // Update status claim
            $claim->update(['status' => 'canceled']);
            
            // Kembalikan jam bonus
            $userBonus = $claim->userBonus;
            $userBonus->bonus_hours_used -= $claim->hours_used;
            $userBonus->status = 'active';
            $userBonus->save();
            
            // Update transaksi meeting
            Transaction::where('id', $claim->meeting_transaction_id)
                ->update(['status' => 'canceled']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Claim cancelled successfully'
        ]);
    }

    /**
     * GET /api/admin/bonus/customers-with-active-bonus
     * List customer dengan monthly bonus yang masih aktif
     */
    public function customersWithActiveBonus()
    {
        try {
            $now = now();
            $currentYear = $now->year;
            $currentMonth = $now->month;
            
            Log::info('🔍 Fetching customers with active bonus', [
                'year' => $currentYear,
                'month' => $currentMonth
            ]);

            // Ambil semua user yang punya bonus aktif
            $customers = User::whereHas('userBonuses', function($query) use ($now) {
                $query->where('status', 'active')
                    ->where('valid_until', '>=', $now)
                    ->whereRaw('bonus_hours_total > bonus_hours_used');
            })
            ->with(['userBonuses' => function($query) use ($now) {
                $query->where('status', 'active')
                    ->where('valid_until', '>=', $now)
                    ->whereRaw('bonus_hours_total > bonus_hours_used');
            }])
            ->get();

            Log::info('📊 Total customers found', [
                'count' => $customers->count(),
                'customer_ids' => $customers->pluck('id')
            ]);

            $result = [];
            
            foreach ($customers as $customer) {
                foreach ($customer->userBonuses as $bonus) {
                    // 🔴 HAPUS filter isMonthlyBonus
                    // if (!$this->isMonthlyBonus($bonus)) {
                    //     continue;
                    // }
                    
                    // Hitung jam yang sudah dipakai bulan ini
                    $usedThisMonth = BonusClaim::where('user_bonus_id', $bonus->id)
                        ->whereYear('claim_date', $currentYear)
                        ->whereMonth('claim_date', $currentMonth)
                        ->sum('hours_used');
                    
                    // Hitung total bulan yang sudah dipakai
                    $monthsUsed = BonusClaim::where('user_bonus_id', $bonus->id)
                        ->select(DB::raw('COUNT(DISTINCT CONCAT(YEAR(claim_date), "-", MONTH(claim_date))) as total_months'))
                        ->first()
                        ->total_months ?? 0;
                    
                    // Hitung sisa jam bulan ini
                    $remainingThisMonth = 4 - $usedThisMonth;
                    
                    // Hitung sisa bulan
                    $remainingMonths = 12 - $monthsUsed;
                    
                    Log::info('👤 Customer bonus details', [
                        'customer' => $customer->name,
                        'bonus_id' => $bonus->id,
                        'used_this_month' => $usedThisMonth,
                        'remaining_this_month' => $remainingThisMonth,
                        'months_used' => $monthsUsed,
                        'remaining_months' => $remainingMonths
                    ]);
                    
                    // Hanya tampilkan jika masih ada sisa bulan
                    if ($remainingMonths > 0) {
                        $result[] = [
                            'user_id' => $customer->id,
                            'name' => $customer->name,
                            'email' => $customer->email,
                            'phone' => $customer->telephone ?? $customer->phone ?? '-',
                            'bonus_id' => $bonus->id,
                            'remaining_hours' => max(0, $remainingThisMonth),
                            'total_hours_this_month' => 4,
                            'used_this_month' => (int)$usedThisMonth,
                            'months_used' => (int)$monthsUsed,
                            'months_remaining' => (int)$remainingMonths,
                            'valid_until' => Carbon::parse($bonus->valid_until)->format('d M Y'),
                            'can_claim' => $remainingThisMonth > 0
                        ];
                    }
                }
            }

            // Urutkan berdasarkan sisa jam terbanyak
            usort($result, function($a, $b) {
                return $b['remaining_hours'] <=> $a['remaining_hours'];
            });

            Log::info('✅ Final result', ['count' => count($result)]);

            return response()->json([
                'success' => true,
                'data' => array_values($result),
                'count' => count($result),
                'current_period' => [
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'month_name' => $now->format('F Y')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ customersWithActiveBonus error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load customers with bonus: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deteksi apakah bonus dari Virtual Office (monthly)
     */
        private function isMonthlyBonus($userBonus): bool
    {
        Log::info('🔍 Checking isMonthlyBonus', [
            'bonus_id' => $userBonus->id,
            'bonus_rule_id' => $userBonus->bonus_rule_id,
            'bonus_hours_total' => $userBonus->bonus_hours_total,
            'notes' => $userBonus->notes
        ]);
        
        // Method 1: Cek dari bonus_rule
        if ($userBonus->bonus_rule_id) {
            $bonusRule = \App\Models\BonusRule::find($userBonus->bonus_rule_id);
            Log::info('   Bonus rule found', [
                'rule_id' => $bonusRule->id ?? null,
                'room_type_trigger' => $bonusRule->room_type_trigger ?? null
            ]);
            
            if ($bonusRule && $bonusRule->room_type_trigger === 'Virtual Office') {
                Log::info('   ✅ Monthly bonus (match by rule)');
                return true;
            }
        }
        
        // Method 2: Cek dari notes
        if (str_contains($userBonus->notes ?? '', 'Virtual Office')) {
            Log::info('   ✅ Monthly bonus (match by notes)');
            return true;
        }
        
        // Method 3: Cek dari total hours (48 jam)
        if ($userBonus->bonus_hours_total == 48) {
            Log::info('   ✅ Monthly bonus (match by total hours)');
            return true;
        }
        
        Log::info('   ❌ Not a monthly bonus');
        return false;
    }
}