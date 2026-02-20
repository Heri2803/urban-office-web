<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserBonus; 
use App\Models\User;

class BonusManagementController extends Controller
{
    /**
     * GET /api/admin/bonus-rules
     * List semua bonus rules
     */
    public function index()
    {
        $rules = BonusRule::with('creator')
            ->orderBy('min_gross_amount')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $rules
        ]);
    }

    /**
     * POST /api/admin/bonus-rules
     * Tambah bonus rule baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type_trigger' => 'required|string|in:Virtual Office',
            'min_gross_amount' => 'required|integer|min:0',
            'bonus_hours' => 'required|integer|min:1',
            'valid_days' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $validated['created_by'] = Auth::id();
        
        $rule = BonusRule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bonus rule created successfully',
            'data' => $rule
        ], 201);
    }

    /**
     * PUT /api/admin/bonus-rules/{id}
     * Update bonus rule
     */
    public function update(Request $request, $id)
    {
        $rule = BonusRule::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'min_gross_amount' => 'sometimes|integer|min:0',
            'bonus_hours' => 'sometimes|integer|min:1',
            'valid_days' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean'
        ]);

        $rule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bonus rule updated successfully',
            'data' => $rule
        ]);
    }

    /**
     * DELETE /api/admin/bonus-rules/{id}
     * Hapus bonus rule (soft delete)
     */
    public function destroy($id)
    {
        $rule = BonusRule::findOrFail($id);
        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bonus rule deleted successfully'
        ]);
    }

    /**
     * POST /api/admin/bonuses/manual-add
     * Admin beri bonus manual ke customer
     */
    public function manualAddBonus(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bonus_hours' => 'required|integer|min:1',
            'valid_days' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $bonus = UserBonus::create([
            'user_id' => $validated['user_id'],
            'bonus_hours_total' => $validated['bonus_hours'],
            'bonus_hours_used' => 0,
            'valid_until' => now()->addDays($validated['valid_days']),
            'status' => 'active',
            'notes' => $validated['notes'] ?? 'Bonus dari admin',
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bonus added successfully',
            'data' => $bonus
        ], 201);
    }
}