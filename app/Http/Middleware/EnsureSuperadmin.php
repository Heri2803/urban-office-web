<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperadmin
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
        
        // 2. Cek role superadmin
        if (Auth::user()->role !== 'superadmin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Superadmin access required.'
                ], 403);
            }
            abort(403, 'Forbidden. Superadmin access required.');
        }
        
        return $next($request);
    }
}