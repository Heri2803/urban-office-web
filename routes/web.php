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
use App\Http\Controllers\Booking\BonusController;
use App\Http\Controllers\Mails\DocumentController; 
use App\Http\Controllers\Backend\Admin\PublicContractController;
use App\Http\Controllers\Booking\ContractController;
use App\Http\Controllers\Booking\SuratController as CustomerSuratController;
use App\Http\Controllers\Notification\BrowserNotificationController;


    Route::get('cities', [BookingApiController::class, 'getCities']);
    Route::get('locations', [BookingApiController::class, 'getLocations']);
    Route::get('rooms', [BookingApiController::class, 'getRooms']);
    Route::get('rooms/{id}', [BookingApiController::class, 'getRoomDetails']);
    Route::get('room-types', [BookingApiController::class, 'getRoomTypes']);
    Route::get('service-prices/{roomId}', [BookingApiController::class, 'getServicePriceByRoom']);
    Route::get('/get-service-price', [ServicePriceController::class, 'getServicePrice']);
    Route::get('/virtual-office-packages', [ServicePriceController::class, 'getVirtualOfficePackages'])->name('virtual-office-packages');
    Route::get('/coworking-passes', [App\Http\Controllers\Booking\ServicePriceController::class, 'getCoworkingPasses']);
    // Event Space Prices
    Route::get('/event-space-prices', [App\Http\Controllers\Booking\ServicePriceController::class, 'getEventSpacePrices']);
    Route::post('/promo/check', [BookingApiController::class, 'checkPromo'])->name('promo.check');

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
Route::get('/policy-modal/{type}', [App\Http\Controllers\PolicyModalController::class, 'show'])->name('policy.modal');
Route::get('/midtrans/return', [PaymentController::class, 'handleReturn'])->name('midtrans.return');

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/finish', [TransactionController::class, 'paymentFinish'])->name('finish');
    Route::get('/error', [TransactionController::class, 'paymentError'])->name('error');
    Route::get('/unfinish', [TransactionController::class, 'paymentUnfinish'])->name('unfinish');
});

Route::post('/transactions/v2', [TransactionController::class, 'storeV2'])
    ->name('transactions.store.v2');

// ============ API BONUS CUSTOMER ============
Route::prefix('api')->name('api.')->middleware('auth')->group(function () {
    Route::prefix('customer/bonus')->name('customer.bonus.')->group(function () {
        Route::get('/', [BonusController::class, 'index']); 
        Route::get('/{id}', [BonusController::class, 'show']);
        Route::get('/usage-history/all', [BonusController::class, 'usageHistory']);
        Route::get('/monthly/overview', [BonusController::class, 'monthlyOverview'])
            ->name('monthly.overview');
    });
    Route::get('/customer/bonus-claims/history', [BonusController::class, 'claimHistory'])
        ->name('customer.bonus-claims.history');

    Route::prefix('browser-notifications')->name('browser-notifications.')->group(function () {
        Route::get('/pending', [BrowserNotificationController::class, 'getPendingNotifications']);
    });

    // ✅ TAMBAHAN: Database Notification API
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/unread-count', [BrowserNotificationController::class, 'unreadCount']);
        Route::post('/{notificationId}/mark-read', [BrowserNotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [BrowserNotificationController::class, 'markAllAsRead']);
    });
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

Route::get('/transaction/{id}', [TransactionController::class, 'show'])
    ->name('dashboard.transaction.show');

Route::get('/check-mitra-status', [MitraAccessController::class, 'checkStatus'])
    ->name('check.mitra.status')
    ->middleware('auth');
Route::get('/lunch-options', [LunchOptionController::class, 'getByLocation']);
Route::get('/lunch-options/location/{locationId}', [LunchOptionController::class, 'getByLocation']);

// Public Deals & Promos Route
Route::get('/deals', [\App\Http\Controllers\Promo\CustomerPromoController::class, 'deals'])->name('deals');

// Dashboard Routes (DILINDUNGI AUTH GUARD) - Semua route dashboard wajib login
Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
    
    Route::get('/home', [App\Http\Controllers\AuthController::class, 'hide'])
        ->name('home');

    Route::get('/calls', function () {
        $user = App\Http\Controllers\AuthController::getUser();
        return view('layouts.dashboard.calls', compact('user'));
    })->name('calls');

    Route::get('/mails', [MailController::class, 'mailContent'])->name('mails');

    // Customer Promo Routes (Private)
    Route::get('/my-vouchers', [\App\Http\Controllers\Promo\CustomerPromoController::class, 'myVouchers'])->name('my-vouchers');
    Route::post('/promo/claim', [\App\Http\Controllers\Promo\CustomerPromoController::class, 'claim'])->name('promo.claim');

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

        // DOCUMENTS ROUTES (Virtual Office)
        Route::prefix('documents')->name('documents.')->group(function () {
            // Get all documents for a transaction
            Route::get('/transaction/{transaction}', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'index'])
                ->name('index');
            
            // Check document status for a transaction
            Route::get('/status/{transaction}', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'status'])
                ->name('status');
            
            // Upload new document
            Route::post('/upload', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'store'])
                ->name('upload');

            Route::put('/{document}', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'update'])
                ->name('update');
            
            Route::patch('/{document}', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'update'])
                ->name('update.patch');
            
            // Delete document (only pending)
            Route::delete('/{document}', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'destroy'])
                ->name('destroy');
            
            // Verify document (admin only - butuh middleware admin)
            Route::post('/{document}/verify', 
                [\App\Http\Controllers\Mails\DocumentController::class, 'verify'])
                ->name('verify')
                ->middleware('admin'); // Pastikan middleware admin ada
        });
    });

    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/search', [InvoiceController::class, 'searchByNumber'])->name('invoice.search');

    Route::get('/reward', function () {
        $user = Auth::user();
        
        // Ambil data transaksi
        $transactions = Transaction::where('user_id', $user->id)
            ->whereIn('status', ['settlement', 'pending', 'failed'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        $totalGross = $transactions->where('status', 'settlement')->sum('gross_amount');
        
        $grossByType = $transactions->where('status', 'settlement')
            ->groupBy('room_type')
            ->map(function($items) {
                return $items->sum('gross_amount');
            });
        
        // ✅ PATH YANG BENAR: layouts.dashboard.reward
        return view('layouts.dashboard.reward', compact('transactions', 'totalGross', 'grossByType'));
        
    })->name('reward');

    Route::get('/bookingform', function () {
        $user = auth()->user();
        if ($user) {
            $user->load(['promos' => function ($q) {
                $q->wherePivot('is_used', false)
                  ->where('status', 'active')
                  ->where('end_date', '>=', now())
                  ->with('type');
            }]);
        }
        
        $publicPromos = App\Models\Promo::where('promo_type_id', 1)
            ->where('status', 'active')
            ->where('is_approved', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('type')
            ->get();
            
        return view('layouts.dashboard.bookingform', compact('user', 'publicPromos'));
    })->name('booking');

    Route::get('/bookinginvoice', function () {
        $user = auth()->user();
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

    Route::prefix('my-surats')->name('surats.')->group(function () {
        Route::get('/', [CustomerSuratController::class, 'index'])->name('index');
        Route::get('/{surat}', [CustomerSuratController::class, 'show'])->name('show');
        Route::get('/{surat}/download', [CustomerSuratController::class, 'download'])->name('download');
        Route::post('/{surat}/mark-read', [CustomerSuratController::class, 'markAsRead'])->name('mark-read');
    });

});

Route::middleware(['auth'])->group(function() {
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

Route::middleware(['auth'])->prefix('customer')->group(function () {
    Route::get('/contracts', [ContractController::class, 'index'])->name('customer.contracts.index');
    Route::get('/contracts/{contract}/download', [ContractController::class, 'download'])->name('customer.contracts.download');
    Route::get('/contracts/{contract}/renew', [ContractController::class, 'renewForm'])->name('customer.contracts.renew.form');
    Route::post('/contracts/{contractId}/renew', [ContractController::class, 'renewProcess'])->name('customer.contracts.renew.process');
    
    // Download Addendum Route
    Route::get('/addendums/{addendum}/download', [ContractController::class, 'downloadAddendum'])->name('customer.addendums.download');
    
    // Renew Addendum Route
    Route::get('/addendums/{addendum}/renew', [ContractController::class, 'renewAddendumForm'])->name('customer.addendums.renew.form');
    Route::post('/addendums/{addendumId}/renew', [ContractController::class, 'renewAddendumProcess'])->name('customer.addendums.renew.process');
});

Route::middleware('throttle:30,1')
    ->get('/contract/verify/{token}', [PublicContractController::class, 'show'])
    ->name('contract.public.verify');

Route::middleware('throttle:30,1')
    ->get('/addendum/verify/{token}', [PublicContractController::class, 'showAddendum'])
    ->name('addendum.public.verify');

// Route untuk clear session manual (untuk development)
Route::get('/clear-session', function () {
    session()->flush();
    return redirect()->route('login')->with('success', 'Session cleared');
})->name('clear.session');


require __DIR__.'/admin.php';
require __DIR__.'/superadmin.php';
require __DIR__.'/mitrapanel.php';