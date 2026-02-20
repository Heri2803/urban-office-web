<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek sudah login
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect()->route('login');
        }
        
        // 2. Cek role admin SAJA (bukan superadmin)
        $user = Auth::user();
        
        if ($user->role !== 'admin') { // ❌ HANYA 'admin' yang boleh
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admin access required'
                ], 403);
            }
            abort(403, 'Forbidden. Admin access required');
        }
        
        return $next($request);
    }
}