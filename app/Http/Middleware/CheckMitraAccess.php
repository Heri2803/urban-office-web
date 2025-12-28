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

        // WAJIB: cek role dengan ketat
        if ($user->role !== 'mitra') {
            return redirect()->route('dashboard.home')
                ->with('error', 'Akses ditolak. Anda bukan mitra.');
        }

        // OPTIONAL tapi penting: cek status mitra harus approved
        if (!$user->mitra || $user->mitra->status !== 'approved') {
            return redirect()->route('dashboard.home')
                ->with('error', 'Status mitra Anda belum aktif.');
        }

        return $next($request);
    }

}