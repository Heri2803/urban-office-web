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
        
        // 2. Cek role admin atau finance
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'finance'])) { // ❌ HANYA 'admin' atau 'finance' yang boleh
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Admin or Finance access required'
                ], 403);
            }
            abort(403, 'Forbidden. Admin or Finance access required');
        }
        
        return $next($request);
    }
}