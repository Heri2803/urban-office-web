<?php
// app/Http/Middleware/CheckMitraAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMitraAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Cek sederhana: punya mitra_id DAN status approved/active
        if (!$user->isMitra()) {
            $status = $user->mitra ? $user->mitra->status : 'Tidak terdaftar';
            return redirect()->route('dashboard.home')
                ->with('error', "Akses ditolak. Status mitra: {$status}");
        }

        return $next($request);
    }
}