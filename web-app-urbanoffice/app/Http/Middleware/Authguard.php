<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthGuard
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login (menggunakan session)
        if (!session('user_logged_in')) {
            // Jika belum login, redirect ke halaman login
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}