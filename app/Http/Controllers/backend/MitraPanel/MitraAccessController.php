<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class MitraAccessController extends Controller
{
    /**
     * Check status mitra via AJAX (untuk button "Masuk sebagai Mitra")
     */
        public function checkStatus(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'has_mitra_access' => false
                ], 401);
            }

            $user = Auth::user();
            
            // ✅ DEBUG LENGKAP
            $debugInfo = [
                'user_id' => $user->id,
                'mitra_id' => $user->mitra_id,
                'mitra_relation_loaded' => !is_null($user->mitra),
                'mitra_status' => $user->mitra ? $user->mitra->status : 'NO_MITRA',
                'isMitra_result' => $user->isMitra(),
                'expected_status' => ['approved', 'active']
            ];

            \Log::info('Mitra Access Debug:', $debugInfo);

            $hasMitraAccess = $user->isMitra();

            return response()->json([
                'has_mitra_access' => $hasMitraAccess,
                'mitra_id' => $user->mitra_id,
                'user_id' => $user->id,
                'role' => $user->role,
                'debug' => $debugInfo, // ✅ RETURN DEBUG INFO
                'message' => $hasMitraAccess 
                    ? 'User memiliki akses mitra' 
                    : 'User tidak memiliki akses mitra. Status: ' . ($user->mitra ? $user->mitra->status : 'NO_MITRA')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in checkStatus: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Terjadi kesalahan server',
                'has_mitra_access' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle redirect ke halaman mitra (untuk button click)
     */
    public function accessMitra(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isMitra()) {
            return redirect()->route('mitrapanel.dashboard');
        }
        
        return redirect()->route('dashboard.prosesmitra')
               ->with('error', 'Anda belum terdaftar sebagai mitra. Silakan daftar terlebih dahulu.');
    }
}