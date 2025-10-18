<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;




class ForgotPasswordController extends Controller
{
    // Tampilkan form forgot password (halaman UI Anda)
    public function showLinkRequestForm()
    {
        // Ganti 'auth.forgot-password' dengan nama view yang benar
        return view('auth.forgot-password'); 
    }

    // TAHAP 1: Kirim Kode OTP ke Email
    public function sendOtp(Request $request)
{
    // 1. Validasi Email
    $request->validate(['email' => 'required|email']);
    $email = $request->email;

    // 2. Cek apakah email terdaftar di tabel users
    $user = User::where('email', $email)->first();
    if (!$user) {
        return response()->json(['message' => 'Email tidak terdaftar.'], 404);
    }

    // 3. Generate Kode OTP (6 digit acak)
    $otp = random_int(100000, 999999);

    // 4. Simpan HASH dari OTP ke tabel password_reset_tokens
    DB::table('password_reset_tokens')->where('email', $email)->delete();
    DB::table('password_reset_tokens')->insert([
        'email'      => $email,
        'token'      => Hash::make($otp),  // <-- HARUS di-hash
        'created_at' => now()
    ]);

    // 5. Kirim OTP asli ke email user (gunakan SendGrid atau Mail::send)
     // Kirim OTP via email
    Mail::to($email)->send(new ResetPasswordOtpMail($otp));

    return response()->json(['message' => 'OTP berhasil dikirim ke email.']);
}


    // TAHAP 2: Verifikasi OTP (Akan dipanggil dari form di dalam modal)
    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    // 1. Ambil record berdasarkan email
    $record = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('created_at', '>', now()->subMinutes(60)) // OTP berlaku 60 menit
        ->first();

    // 2. Cek apakah ada record dan cocok dengan OTP
    if (!$record || !Hash::check($request->otp, $record->token)) {
        return response()->json(['message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'], 400);
    }

    // 3. Hapus OTP lama
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    // 4. Buat token baru untuk reset password
    $reset_token = Str::random(60);
    DB::table('password_reset_tokens')->insert([
        'email' => $request->email,
        'token' => Hash::make($reset_token),
        'created_at' => now()
    ]);

    // 5. Kirim response sukses dengan redirect URL
    return response()->json([
        'message' => 'Verifikasi berhasil.',
        'redirect_url' => route('password.reset', [
            'token' => $reset_token,
            'email' => $request->email
        ])
    ]);
}


// TAHAP 3: Proses Reset Password (setelah user mengisi form ganti password)
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', PasswordRule::defaults()],
    ]);

    // 1. Ambil record token dari DB
    $reset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (!$reset) {
        return back()->withErrors(['email' => 'Token reset tidak ditemukan.']);
    }

    // 2. Cek apakah token valid (pakai Hash::check, karena di-sendOtp() sudah di-hash)
    if (!Hash::check($request->token, $reset->token)) {
        return back()->withErrors(['token' => 'Token reset tidak valid.']);
    }

    // 3. Update password user
    $user = User::where('email', $request->email)->first();
    if ($user) {
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // 4. Hapus token setelah dipakai
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        event(new PasswordReset($user));
    }

    return redirect()->route('login')->with('status', 'Password berhasil direset, silakan login dengan password baru.');
}

}