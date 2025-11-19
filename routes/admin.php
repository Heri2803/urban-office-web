<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\DashboardController;
use App\Http\Controllers\Backend\Admin\ServicePhotoController;
use App\Http\Controllers\Admin\BannerPromoController;
use App\Http\Controllers\Backend\Admin\ServiceHighlightController;
use App\Http\Controllers\Backend\Admin\RoomStatusController;
use App\Http\Controllers\Backend\Admin\BannerController; // ADD THIS

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

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

// Content Management Routes
Route::get('/service-photos', function () {
    return view('layouts.admin.service-photos');
})->name('admin.content.service-photos');

// ✅ DAN TETAP PERTAHANKAN ROUTE GROUP UNTUK API:
Route::prefix('service-photos')->name('service-photos.')->group(function () {
    // API Routes for Service Photos
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/room-types', [ServicePhotoController::class, 'getRoomTypes'])->name('room-types');
        Route::get('/photos', [ServicePhotoController::class, 'index'])->name('photos');
        Route::post('/upload', [ServicePhotoController::class, 'store'])->name('upload');
        Route::post('/bulk-upload', [ServicePhotoController::class, 'bulkUpload'])->name('bulk-upload');
        Route::put('/{servicePhoto}', [ServicePhotoController::class, 'update'])->name('update');
        Route::put('/{servicePhoto}/set-primary', [ServicePhotoController::class, 'setPrimary'])->name('set-primary');
        Route::delete('/{servicePhoto}', [ServicePhotoController::class, 'destroy'])->name('destroy');
    });
});


Route::prefix('highlights/api')->name('highlights.api.')->group(function () {
    // Custom routes harus di atas
    Route::get('/data', [ServiceHighlightController::class, 'getInitialData'])->name('data');
    Route::post('/reorder', [ServiceHighlightController::class, 'reorder'])->name('reorder');
    Route::post('/{serviceHighlight}/toggle-status', [ServiceHighlightController::class, 'toggleStatus'])->name('toggle-status');
    
    // Resource routes
    Route::get('/', [ServiceHighlightController::class, 'index'])->name('index');
    Route::post('/', [ServiceHighlightController::class, 'store'])->name('store');
    Route::get('/{serviceHighlight}', [ServiceHighlightController::class, 'show'])->name('show');
    Route::put('/{serviceHighlight}', [ServiceHighlightController::class, 'update'])->name('update');
    Route::delete('/{serviceHighlight}', [ServiceHighlightController::class, 'destroy'])->name('destroy');
});

// 🔥 BANNER PROMO MANAGEMENT ROUTES
Route::prefix('banners')->name('banners.')->group(function () {
    Route::get('/', [BannerController::class, 'index'])->name('index');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/', [BannerController::class, 'store'])->name('store');
    Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
    Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
    Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
    
    // Additional routes for banner management
    Route::post('/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/api/data', [BannerController::class, 'apiIndex'])->name('api');
    Route::get('/api/categories', [BannerController::class, 'getCategories'])->name('categories');
    Route::get('/api/locations', [BannerController::class, 'getLocations'])->name('locations');
    Route::get('/api/types', [BannerController::class, 'getPromoTypes'])->name('types');
});

// routes/api.php
Route::prefix('room-management')->group(function () {
    Route::get('unique-rooms', [RoomStatusController::class, 'getUniqueRooms']);
    Route::get('all-status', [RoomStatusController::class, 'getAllRoomsStatus']);
    Route::get('{roomId}/status', [RoomStatusController::class, 'getRoomStatus']);
    Route::get('{roomId}/active-booking', [RoomStatusController::class, 'getActiveBooking']);
    Route::get('{roomId}/todays-booking', [RoomStatusController::class, 'getTodaysBooking']);
    Route::get('{roomId}/next-booking', [RoomStatusController::class, 'getNextBooking']);
    Route::post('{roomId}/check-maintenance-conflicts', [RoomStatusController::class, 'checkMaintenanceConflicts']);
    Route::post('bulk-status', [RoomStatusController::class, 'getBulkRoomStatus']);
    Route::post('rooms', [RoomStatusController::class, 'createRoom']);
    Route::put('rooms/{id}', [RoomStatusController::class, 'updateRoom']);
    Route::delete('rooms/{id}', [RoomStatusController::class, 'deleteRoom']);
    Route::post('rooms/{id}/maintenance', [RoomStatusController::class, 'setMaintenance']);
});

Route::get('/highlights', function () {
    return view('layouts.admin.service-highlight');
})->name('admin.content.highlights');

// Pricing
Route::get('/pricing', function () {
    return view('layouts.admin.pricing');
})->name('admin.pricing');

// Dashboard API routes
Route::get('/dashboard/transactions', [DashboardController::class, 'getTransactions'])->name('dashboard.transactions');
Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');