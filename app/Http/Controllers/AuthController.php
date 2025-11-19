<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        if (session('user_logged_in')) {
            return redirect()->route('dashboard.home');
        }

        return view('layouts.auth.login');
    }

    // Proses login (pakai database) - MODIFIKASI INI
        public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // ❌ HAPUS REDIRECT LANGSUNG KE MITRA
            // if ($user->isMitra()) {
            //     return redirect()->route('mitrapanel.dashboard')
            //         ->with('success', 'Login berhasil! Selamat datang di Panel Mitra.');
            // }
            
            // ✅ SELALU REDIRECT KE CUSTOMER DASHBOARD DULU
            return redirect()->route('dashboard.home')
                ->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('layouts.auth.register');
    }

    // Proses register (simpan user baru ke database)
    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $profilePath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'profile_photo' => $profilePath,
            // mitra_id akan null by default (customer)
        ]);

        // Auto login setelah register
        Auth::login($user); // ← GUNAKAN Auth::login() untuk consistency
        
        $request->session()->regenerate();

        return redirect()->route('dashboard.home')
            ->with('success', 'Registrasi berhasil!');
    }

    // Proses logout
    public function logout()
    {
        Auth::logout();
        session()->flush();
        
        return redirect()->route('login')
            ->with('success', 'Logout berhasil!');
    }

    // Ambil user info dari session - OPTIONAL (bisa pakai auth() langsung)
    public static function getUser()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        return [
            'id'         => $user->id,
            'email'      => $user->email,
            'name'       => $user->name,
            'is_mitra'   => $user->isMitra(),
            'login_time' => now()->toDateTimeString()
        ];
    }
    
     public function hide()
        {
            $userId = auth()->id();
            
            \Log::info('=== Dashboard Home Accessed ===');
            \Log::info('User ID: ' . $userId);
            
            // Total transaksi user
            $totalTransactions = \App\Models\Transaction::where('user_id', $userId)->count();
            \Log::info('Total transactions: ' . $totalTransactions);
            
            // Hitung transaksi yang belum dibaca
            $unreadTransactions = \App\Models\Transaction::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
            
            \Log::info('Unread transactions: ' . $unreadTransactions);
            
            // Debug: ambil sample data
            $sampleUnread = \App\Models\Transaction::where('user_id', $userId)
                ->where('is_read', false)
                ->select('id', 'order_id', 'is_read', 'created_at')
                ->limit(3)
                ->get();
            
            \Log::info('Sample unread transactions:', $sampleUnread->toArray());
            
            $user = AuthController::getUser();
            
            return view('layouts.dashboard.home', compact('user', 'unreadTransactions'));
        }

}