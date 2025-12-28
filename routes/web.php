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
use Illuminate\Support\Facades\Artisan;
use App\Models\Mitra;
use App\Mail\MitraNotificationMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionSettledMail;
use App\Models\Transaction;
use App\Http\Controllers\Booking\BookingApiController;
use App\Http\Controllers\Booking\ServicePriceController;
use App\Http\Controllers\Booking\PaymentController;
use App\Http\Controllers\Backend\MitraPanel\MitraAccessController;
use App\Http\Controllers\Booking\LunchOptionController;


    Route::get('cities', [BookingApiController::class, 'getCities']);
    Route::get('locations', [BookingApiController::class, 'getLocations']);
    Route::get('rooms', [BookingApiController::class, 'getRooms']);
    Route::get('rooms/{id}', [BookingApiController::class, 'getRoomDetails']);
    Route::get('room-types', [BookingApiController::class, 'getRoomTypes']);
    Route::get('service-prices/{roomId}', [BookingApiController::class, 'getServicePriceByRoom']);
    Route::get('/get-service-price', [ServicePriceController::class, 'getServicePrice']);
    Route::get('/virtual-office-packages', [ServicePriceController::class, 'getVirtualOfficePackages']);
    Route::get('/coworking-passes', [App\Http\Controllers\Booking\ServicePriceController::class, 'getCoworkingPasses']);
    // Event Space Prices
    Route::get('/event-space-prices', [App\Http\Controllers\Booking\ServicePriceController::class, 'getEventSpacePrices']);

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

Route::post('/transactions/v2', [TransactionController::class, 'storeV2'])
    ->name('transactions.store.v2');

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

Route::get('/transaction/{id}', [TransactionController::class, 'show'])
    ->name('dashboard.transaction.show');

Route::get('/check-mitra-status', [MitraAccessController::class, 'checkStatus'])
    ->name('check.mitra.status')
    ->middleware('auth');

Route::get('/lunch-options', [LunchOptionController::class, 'getByLocation']);
Route::get('/lunch-options/location/{locationId}', [LunchOptionController::class, 'getByLocation']);

// Dashboard Routes (DILINDUNGI AUTH GUARD) - Semua route dashboard wajib login
Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
    
    Route::get('/home', [App\Http\Controllers\AuthController::class, 'hide'])
        ->name('home');

    Route::get('/calls', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.calls', compact('user'));
    })->name('calls');

    Route::get('/mails', [MailController::class, 'mailContent'])->name('mails');

    Route::prefix('mails')->name('mails.')->group(function () {
        Route::get('/transaction/{transaction}/messages', 
            [\App\Http\Controllers\Mails\CustomerMessageController::class, 'getTransactionMessages'])
            ->name('transaction.messages.get');
        
        Route::post('/transaction/{transaction}/message', 
            [\App\Http\Controllers\Mails\CustomerMessageController::class, 'store'])
            ->name('transaction.message.store');
        
        Route::post('/transaction/{transaction}/mark-read', 
            [\App\Http\Controllers\Mails\CustomerMessageController::class, 'markAsRead'])
            ->name('transaction.mark-read');
    });

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

    Route::get('/mitra/access', [MitraAccessController::class, 'accessMitra'])
        ->name('mitra.access');

    Route::get('/mitra', function () {
        $user = auth()->user(); // ✅ GUNAKAN auth() HELPER
        return view('layouts.dashboard.mitra', compact('user'));
    })->name('mitra');

    Route::get('/prosesmitra', function () {
        $user = auth()->user(); // ✅ GUNAKAN auth() HELPER
        return view('layouts.dashboard.prosesmitra', compact('user'));
    })->name('prosesmitra');

    Route::get('/mitraform', function () {
        $user = auth()->user(); // ✅ GUNAKAN auth() HELPER
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

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "Cache Laravel sudah dibersihkan ðŸš€";
});

// Route untuk clear session manual (untuk development)
Route::get('/clear-session', function () {
    session()->flush();
    return redirect()->route('login')->with('success', 'Session cleared');
})->name('clear.session');


require __DIR__.'/admin.php';
require __DIR__.'/superadmin.php';
require __DIR__.'/mitrapanel.php';