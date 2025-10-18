<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Superadmin\DashboardController;

Route::get('/superadmin/dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

Route::get('/superadmin/mitra/all', function () {
    return view('layouts.superadmin.mitra-all');
})->name('superadmin.mitra.all');

Route::get('/superadmin/branches/all', function () {
    return view('layouts.superadmin.branches-all');
})->name('superadmin.branches.all');

Route::get('/superadmin/branches/settings', function () {
    return view('layouts.superadmin.branches-settings');
})->name('superadmin.branches.settings');

Route::get('/superadmin/mitra/revenue-mitra', function () {
    return view('layouts.superadmin.revenue-share-mitra');
})->name('superadmin.mitra.revenue-mitra');

// Halaman Kelola User
Route::get('/superadmin/users', function () {
    return view('superadmin.users');
})->name('superadmin.users');

Route::get('/superadmin/user-management/user-manage-all', function () {
        return view('layouts.superadmin.user-manage-all');
    })->name('superadmin.user-management.user-manage-all');

Route::get('/superadmin/user-management/user-activitylog-manage', function () {
        return view('layouts.superadmin.user-activitylog-manage');
    })->name('superadmin.user-management.user-activitylog-manage');

Route::get('/superadmin/Booking/reports', function () {
        return view('layouts.superadmin.booking-reports');
    })->name('superadmin.booking.reports');
Route::get('/superadmin/Booking/all', function () {
        return view('layouts.superadmin.booking-all');
    })->name('superadmin.booking.all');

// Halaman Laporan
Route::get('/superadmin/room-management/all', function () {
    return view('layouts.superadmin.room-manage-all');
})->name('superadmin.room-management.all');

Route::get('/superadmin/content-management/photos', function () {
    return view('layouts.superadmin.content-photos');
})->name('superadmin.content-management.photos');

Route::get('/superadmin/content-management/banner', function () {
    return view('layouts.superadmin.content-banner');
})->name('superadmin.content-management.banner');

Route::get('/superadmin/content-management/highlight', function () {
    return view('layouts.superadmin.content-highlight');
})->name('superadmin.content-management.highlight');

Route::get('/superadmin/pricing-management/master', function () {
    return view('layouts.superadmin.pricing-master');
})->name('superadmin.pricing-management.master');

Route::get('/superadmin/pricing-management/branch', function () {
    return view('layouts.superadmin.pricing-branch');
})->name('superadmin.pricing-management.branch');

Route::get('/superadmin/pricing-management/approval', function () {
    return view('layouts.superadmin.pricing-approval');
})->name('superadmin.pricing-management.approval');

Route::get('/superadmin/voucher-promo/create', function () {
    return view('layouts.superadmin.voucher-promo-create');
})->name('superadmin.voucher-promo.create');

Route::get('/superadmin/voucher-promo/active', function () {
    return view('layouts.superadmin.voucher-promo-active');
})->name('superadmin.voucher-promo.active');

Route::get('/superadmin/voucher-promo/usage', function () {
    return view('layouts.superadmin.voucher-promo-usage');
})->name('superadmin.voucher-promo.usage');

// Halaman Laporan
Route::get('/superadmin/reports', function () {
    return view('superadmin.reports');
})->name('superadmin.reports');

// Halaman Profil
Route::get('/superadmin/profile', function () {
    return view('superadmin.profile');
})->name('superadmin.profile');