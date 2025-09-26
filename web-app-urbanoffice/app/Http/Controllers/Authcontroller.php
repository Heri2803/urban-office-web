<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    // Proses login (pakai database)
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Simpan ke session
            session([
                'user_logged_in' => true,
                'user_id'        => $user->id,
                'user_email'     => $user->email,
                'user_name'      => $user->name,
                'login_time'     => now()->toDateTimeString()
            ]);

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
        ]);

        // Auto login setelah register
        session([
            'user_logged_in' => true,
            'user_id'        => $user->id,
            'user_email'     => $user->email,
            'user_name'      => $user->name,
            'login_time'     => now()->toDateTimeString()
        ]);

        return redirect()->route('dashboard.home')
            ->with('success', 'Registrasi berhasil!');
    }

    // Proses logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')
            ->with('success', 'Logout berhasil!');
    }

    // Ambil user info dari session
    public static function getUser()
    {
        if (!session('user_logged_in')) {
            return null;
        }

        return [
            'id'         => session('user_id'),
            'email'      => session('user_email'),
            'name'       => session('user_name'),
            'login_time' => session('login_time')
        ];
    }
}
