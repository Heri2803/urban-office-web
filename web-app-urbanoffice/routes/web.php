<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Midtrans\Config;
use Midtrans\Snap;
use App\Http\Controllers\Booking\TransactionController;
use App\Http\Controllers\Booking\InvoiceController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Mails\MailController;
use App\Http\Controllers\Mitra\MitraController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;


Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::post('/midtrans/callback', [TransactionController::class, 'callback'])->name('midtrans.callback');
Route::post('/midtrans/notification', [TransactionController::class, 'notificationHandler']);
Route::get('/invoice/{order_id}', [InvoiceController::class, 'generate'])->name('invoice.generate');
Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::put('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::get('/profile-status', [ProfileController::class, 'showDashboard'])->name('profile.dashboard')->middleware('auth');
Route::post('/dashboard/mitra', [MitraController::class, 'store'])->name('dashboard.mitra');
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/midtrans/return', [PaymentController::class, 'handleReturn'])->name('midtrans.return');

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/finish', [TransactionController::class, 'paymentFinish'])->name('finish');
    Route::get('/error', [TransactionController::class, 'paymentError'])->name('error');
    Route::get('/unfinish', [TransactionController::class, 'paymentUnfinish'])->name('unfinish');
});

Route::get('/test-midtrans', function () {
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $params = [
        'transaction_details' => [
            'order_id' => rand(),
            'gross_amount' => 10000,
        ],
        'customer_details' => [
            'first_name' => 'Test',
            'email' => 'test@example.com',
        ],
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return view('midtrans-test', compact('snapToken'));
});

// Root route - redirect berdasarkan status login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.home');
    }
    return redirect()->route('beforelogin');
});

// Auth Routes (tidak perlu middleware auth) - HALAMAN PUBLIK
Route::get('/beforelogin', function () {
    return view('layouts.auth.beforelogin');
})->name('beforelogin');

Route::get('/register', function () {
    return view('layouts.auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::get('/forgotpassword', function () {
    return view('layouts.auth.forgotpassword');
})->name('password.request');

// Route API/POST untuk mengirim OTP
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.send_otp');

// Route API/POST untuk verifikasi OTP (dari modal)
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify_otp');

// Route standar Laravel untuk form Ganti Password (setelah verifikasi OTP berhasil)
// Ini harus merujuk ke method yang menangani tampilan form reset password (biasanya di Auth Controller)
Route::get('/reset-password/{token}', function ($token) {
    // Anda harus membuat view ini untuk form ganti password
    return view('layouts.auth.reset-password', ['token' => $token, 'email' => request()->email]); 
})->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'store'])
    ->name('password.update');


// Login routes dengan controller
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Logout route dengan controller
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Components route (tidak perlu auth)
Route::get('/components/sidebar', function () {     
    return response(view('layouts.components.sidebar')->render())
           ->header('Content-Type', 'text/html');
})->name('sidebar');

// Dashboard Routes (DILINDUNGI AUTH GUARD) - Semua route dashboard wajib login
Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
    
    Route::get('/home', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.home', compact('user'));
    })->name('home');

    Route::get('/calls', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.calls', compact('user'));
    })->name('calls');

    Route::get('/mails', [MailController::class, 'mailContent'])->name('mails');

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');

    Route::get('/reward', [TransactionController::class, 'index'])->name('reward');

    Route::get('/bookingform', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.bookingform', compact('user'));
    })->name('booking');

    Route::get('/bookinginvoice', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.bookinginvoice', compact('user'));
    })->name('bookinginvoice');

    Route::get('/mitra', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.mitra', compact('user'));
    })->name('mitra');

    Route::get('/prosesmitra', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.prosesmitra', compact('user'));
    })->name('prosesmitra');

    Route::get('/mitraform', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.mitraform', compact('user'));
    })->name('mitraform');

    Route::get('/profile', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.profile', compact('user'));
    })->name('profile');

});

Route::middleware(['auth'])->group(function() {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});


// Route khusus untuk testing (bisa dihapus nanti)
Route::get('/test-session', function () {
    return [
        'logged_in' => session('user_logged_in', false),
        'user_email' => session('user_email', 'none'),
        'user_name' => session('user_name', 'none'),
        'all_session' => session()->all()
    ];
})->name('test.session');

// Route untuk clear session manual (untuk development)
Route::get('/clear-session', function () {
    session()->flush();
    return redirect()->route('login')->with('success', 'Session cleared');
})->name('clear.session');