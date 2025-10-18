<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\DashboardController;
use App\Http\Controllers\Admin\ServicePhotoController;
use App\Http\Controllers\Admin\BannerPromoController;
use App\Http\Controllers\Admin\ServiceHighlightController;


Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/content', [ContentController::class, 'index'])->name('admin.content');

    Route::get('/admin/settings', function () {
        return view('layouts.admin.settings');
    })->name('admin.settings');
    // Booking routes
    Route::get('/booking/all', function () {
        return view('layouts.admin.booking-all');
    })->name('admin.booking.all');
    
    Route::get('/booking/room-assignment', function () {
        return view('layouts.admin.room-assignment');
    })->name('admin.booking.room-assignment');

    Route::get('/booking/service-confirmation', function () {
        return view('layouts.admin.service-confirmation');
    })->name('admin.booking.service-confirmation');

    Route::get('/booking/booking-history', function () {
        return view('layouts.admin.booking-history');
    })->name('admin.booking.history');
    Route::get('/booking/walk-in-booking', function () {
        return view('layouts.admin.walk-in-booking');
    })->name('admin.booking.walk-in-booking');


    // Rooms
    Route::get('/room-manegement', function () {
        return view('layouts.admin.room-management');
    })->name('admin.room-management');
    Route::get('/service-photos', function () {
            return view('layouts.admin.service-photos');
        })->name('admin.content.service-photos');

        Route::get('/banners', function () {
            return view('layouts.admin.banners-promo');
        })->name('admin.content.banners');

        Route::get('/highlights', function () {
            return view('layouts.admin.service-highlight');
        })->name('admin.content.highlights');

    // Pricing
    Route::get('/pricing', function () {
        return view('layouts.admin.pricing');
    })->name('admin.pricing');

    

Route::get('/dashboard/transactions', [DashboardController::class, 'getTransactions'])->name('dashboard.transactions');
Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
