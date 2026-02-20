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
     * List semua customer yang memiliki bonus aktif
     */
        public function customersWithActiveBonus()
    {
        try {
            Log::info('=== START customersWithActiveBonus ===');
            
            // ✅ Step 1: Cek total users dengan bonus
            $totalUsersWithBonus = User::whereHas('userBonuses')->count();
            Log::info('Total users with any bonus:', ['count' => $totalUsersWithBonus]);
            
            // ✅ Step 2: Cek users dengan active bonus
            $activeCount = User::whereHas('userBonuses', function($query) {
                $query->where('status', 'active');
            })->count();
            Log::info('Users with active bonus:', ['count' => $activeCount]);
            
            // ✅ Step 3: Cek yang valid_until masih berlaku
            $validCount = User::whereHas('userBonuses', function($query) {
                $query->where('status', 'active')
                    ->where('valid_until', '>=', now());
            })->count();
            Log::info('Users with valid bonus:', ['count' => $validCount]);
            
            // ✅ Step 4: Cek yang masih punya sisa jam
            $withHoursCount = User::whereHas('userBonuses', function($query) {
                $query->where('status', 'active')
                    ->where('valid_until', '>=', now())
                    ->whereRaw('bonus_hours_total > bonus_hours_used');
            })->count();
            Log::info('Users with remaining hours:', ['count' => $withHoursCount]);
            
            // ✅ Query lengkap dengan logging
            $customers = User::whereHas('userBonuses', function($query) {
                    $query->where('status', 'active')
                        ->where('valid_until', '>=', now())
                        ->whereRaw('bonus_hours_total > bonus_hours_used');
                })
                ->with(['userBonuses' => function($query) {
                    $query->where('status', 'active')
                        ->where('valid_until', '>=', now())
                        ->whereRaw('bonus_hours_total > bonus_hours_used')
                        ->orderBy('valid_until', 'asc');
                }])
                ->get();
            
            Log::info('Users fetched from query:', ['count' => $customers->count()]);
            
            // ✅ Log setiap user
            $customers->each(function($user, $index) {
                Log::info("User #{$index}", [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bonuses_count' => $user->userBonuses->count()
                ]);
            });
            
            $mappedCustomers = $customers->map(function($user) {
                    $bonus = $user->userBonuses->sortByDesc(function($b) {
                        return $b->bonus_hours_total - $b->bonus_hours_used;
                    })->first();
                    
                    // ✅ Log bonus yang dipilih
                    Log::info("Selected bonus for user {$user->id}", [
                        'bonus_id' => $bonus->id,
                        'remaining' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                        'total' => $bonus->bonus_hours_total,
                        'used' => $bonus->bonus_hours_used
                    ]);
                    
                    return [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->telephone ?? $user->phone ?? '-',
                        'bonus_id' => $bonus->id,
                        'remaining_hours' => $bonus->bonus_hours_total - $bonus->bonus_hours_used,
                        'total_hours' => $bonus->bonus_hours_total,
                        'valid_until' => $bonus->valid_until->format('d M Y')
                    ];
                })
                ->sortByDesc('remaining_hours')
                ->values();
            
            Log::info('Final mapped customers:', ['count' => $mappedCustomers->count()]);
            Log::info('=== END customersWithActiveBonus ===');

            return response()->json([
                'success' => true,
                'data' => $mappedCustomers,
                'count' => $mappedCustomers->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('customersWithActiveBonus ERROR', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}