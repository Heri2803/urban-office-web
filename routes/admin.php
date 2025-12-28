<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\ServicePhotoController;
use App\Http\Controllers\Admin\BannerPromoController;
use App\Http\Controllers\Backend\Admin\ServiceHighlightController;
use App\Http\Controllers\Backend\Admin\RoomStatusController;
use App\Http\Controllers\Backend\Admin\BannerController;
use App\Http\Controllers\Backend\Admin\RoomAssignController;
use App\Http\Controllers\Backend\Admin\ServiceConfirmationController;
use App\Http\Controllers\Backend\Admin\BookingController;
use App\Http\Controllers\Backend\Admin\AdminSettingsController;
use App\Http\Controllers\Backend\Admin\MessageController;
use App\Http\Controllers\Backend\Admin\ExistingCustomerBookingController;
use App\Http\Controllers\Backend\Admin\AdminServicePriceController;

Route::get('/admin/dashboard', [BookingController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard/stats', [BookingController::class, 'getDashboardStats'])->name('dashboard.stats');
Route::get('/dashboard/chart-data', [BookingController::class, 'getChartData'])->name('dashboard.chart-data');

Route::get('/admin/messages', function () {
    return view('layouts.admin.message-page');
})->name('admin.messages');

// ✅ TAMBAHKAN ROUTES MESSAGING DI SINI - SETELAH DASHBOARD, SEBELUM SETTINGS
Route::prefix('admin/messages')->middleware(['auth'])->group(function () {
    Route::get('/users', [MessageController::class, 'getAllMessageUsers']);
    Route::get('/conversations', [MessageController::class, 'getMessageHistory']);
    Route::post('/broadcast', [MessageController::class, 'broadcastToAll']);
    Route::post('/mark-read', [MessageController::class, 'markAsRead']);
    Route::post('/send/{userId}', [MessageController::class, 'sendToUser']);
    Route::get('/conversation/{userId}', [MessageController::class, 'getConversation']);
    Route::put('/{messageId}/update', [MessageController::class, 'updateMessage']);
    Route::delete('/{messageId}', [MessageController::class, 'deleteMessage']);
    Route::delete('/{messageId}/for-me', [MessageController::class, 'deleteMessageForMe']);
});

// ✅ ADMIN SETTINGS ROUTES
Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
    Route::get('/', [AdminSettingsController::class, 'show'])->name('index');
    Route::put('/profile', [AdminSettingsController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AdminSettingsController::class, 'updatePassword'])->name('password.update');
    Route::post('/avatar', [AdminSettingsController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [AdminSettingsController::class, 'removeAvatar'])->name('avatar.remove');
});

// ✅ Route utama untuk admin.settings
Route::get('/admin/settings', [AdminSettingsController::class, 'show'])->name('admin.settings');

// ✅ BOOKING ROUTES - TAMBAHKAN EXISTING CUSTOMER PAGE
Route::get('/booking/all', function () {
    return view('layouts.admin.booking-all');
})->name('admin.booking.all');

Route::get('/booking/room-assignment', [RoomAssignController::class, 'index'])->name('admin.booking.room-assignment');

Route::get('/booking/service-confirmation', [ServiceConfirmationController::class, 'index'])->name('admin.booking.service-confirmation');

Route::get('/booking/booking-history', function () {
    return view('layouts.admin.booking-history');
})->name('admin.booking.history');

Route::get('/booking/walk-in-booking', function () {
    return view('layouts.admin.walk-in-booking');
})->name('admin.booking.walk-in-booking');

// ✅ TAMBAHKAN: Existing Customer Booking Page
Route::get('/booking/existing-customer', [ExistingCustomerBookingController::class, 'index'])->name('admin.booking.existing-customer');

// ✅ BOOKING API ROUTES GROUP
Route::prefix('booking')->name('admin.booking.')->group(function () {
    
    // Room Assignment API
    Route::get('/room-assignment/api/rooms-status', [RoomAssignController::class, 'getRoomsWithStatus'])->name('api.rooms-status');
    Route::get('/room-assignment/api/available-customers', [RoomAssignController::class, 'getAvailableCustomers'])->name('api.available-customers');

    // ✅ EXISTING CUSTOMER API ROUTES - PERBAIKI STRUKTURNYA
    Route::prefix('existing-customer/api')->name('existing-customer.api.')->group(function () {
        Route::get('/search', [ExistingCustomerBookingController::class, 'searchCustomers'])->name('search');
        Route::get('/{userId}/history', [ExistingCustomerBookingController::class, 'getCustomerHistory'])->name('history');
        Route::get('/{userId}/loyalty-discount', [ExistingCustomerBookingController::class, 'calculateLoyaltyDiscount'])->name('loyalty-discount');
        Route::get('/{userId}/stats', [ExistingCustomerBookingController::class, 'getCustomerStats'])->name('stats');
        Route::get('/room-types', [ExistingCustomerBookingController::class, 'getRoomTypes'])->name('room-types');
        Route::post('/create', [ExistingCustomerBookingController::class, 'store'])->name('create');
        Route::get('/services/available', [ExistingCustomerBookingController::class, 'getAvailableServices']);
        Route::get('/services/{type}/packages', [ExistingCustomerBookingController::class, 'getPackages']);
        Route::get('/available-times', [ExistingCustomerBookingController::class, 'getAvailableTimeSlots']);
        Route::get('/facilities', [ExistingCustomerBookingController::class, 'getAvailableFacilities']);
        Route::get('/service-price', [ExistingCustomerBookingController::class, 'getServicePrice']); 
        Route::get('/available-rooms', [ExistingCustomerBookingController::class, 'getAvailableRooms']);
        Route::get('/coworking-passes', [ExistingCustomerBookingController::class, 'getCoworkingPasses']);
        Route::get('/virtual-office-packages', [ExistingCustomerBookingController::class, 'getVirtualOfficePackages']);
        Route::get('/event-packages', [ExistingCustomerBookingController::class, 'getEventPackages']);
    });

    // All Bookings API
    Route::prefix('all')->name('all.')->group(function () {
        Route::get('/api/data', [BookingController::class, 'getAllBookings'])->name('api.data');
        Route::get('/api/filter-options', [BookingController::class, 'getFilterOptions'])->name('api.filter-options');
        Route::get('/api/export', [BookingController::class, 'exportExcel'])->name('api.export');
    });
    
    // Service Confirmation API
    Route::prefix('service-confirmation')->name('service-confirmation.')->group(function () {
        Route::post('/{id}/confirm', [ServiceConfirmationController::class, 'confirmBooking'])->name('confirm');
        Route::post('/{id}/cancel', [ServiceConfirmationController::class, 'cancelConfirmation'])->name('cancel');
        Route::get('/api/data', [ServiceConfirmationController::class, 'getBookingData'])->name('api.data');
    });
});

// Rooms
Route::get('/room-manegement', function () {
    return view('layouts.admin.room-management');
})->name('admin.room-management');

// Content Management Routes
Route::get('/service-photos', function () {
    return view('layouts.admin.service-photos');
})->name('admin.content.service-photos');

// ✅ SERVICE PHOTOS API
Route::prefix('service-photos')->name('service-photos.')->group(function () {
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

// ✅ HIGHLIGHTS API
Route::prefix('highlights/api')->name('highlights.api.')->group(function () {
    Route::get('/data', [ServiceHighlightController::class, 'getInitialData'])->name('data');
    Route::post('/reorder', [ServiceHighlightController::class, 'reorder'])->name('reorder');
    Route::post('/{serviceHighlight}/toggle-status', [ServiceHighlightController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/', [ServiceHighlightController::class, 'index'])->name('index');
    Route::post('/', [ServiceHighlightController::class, 'store'])->name('store');
    Route::get('/{serviceHighlight}', [ServiceHighlightController::class, 'show'])->name('show');
    Route::put('/{serviceHighlight}', [ServiceHighlightController::class, 'update'])->name('update');
    Route::delete('/{serviceHighlight}', [ServiceHighlightController::class, 'destroy'])->name('destroy');
});

// ✅ BANNER PROMO MANAGEMENT
Route::prefix('banners')->name('banners.')->group(function () {
    Route::get('/', [BannerController::class, 'index'])->name('index');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/', [BannerController::class, 'store'])->name('store');
    Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
    Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
    Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
    
    Route::post('/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/api/data', [BannerController::class, 'apiIndex'])->name('api');
    Route::get('/api/categories', [BannerController::class, 'getCategories'])->name('categories');
    Route::get('/api/locations', [BannerController::class, 'getLocations'])->name('locations');
    Route::get('/api/types', [BannerController::class, 'getPromoTypes'])->name('types');
});

// ✅ ROOM MANAGEMENT API
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

Route::get('/pricing', function () {
    return view('layouts.admin.pricing');
})->name('admin.pricing');

Route::prefix('pricing')->name('admin.pricing.')->group(function () {
    
    // ======================
    // DASHBOARD & STATISTICS
    // ======================
    Route::get('/api/dashboard-stats', [AdminServicePriceController::class, 'dashboardStats'])
        ->name('api.dashboard-stats');
    
    // ======================
    // PRICE VIEWS (READ ONLY)
    // ======================
    // View by service category
    Route::get('/api/default-prices', [AdminServicePriceController::class, 'getDefaultPrices'])
        ->name('api.default-prices');
    
    Route::get('/api/default-prices/{categorySlug}', [AdminServicePriceController::class, 'getCategoryDefaultPrices'])
        ->name('api.category-default-prices');
    
    // View by room type (utama untuk admin)
    Route::get('/api/prices-by-roomtype', [AdminServicePriceController::class, 'getPricesByRoomType'])
        ->name('api.prices-by-roomtype');
    
    Route::get('/api/roomtype/{roomTypeId}/prices', [AdminServicePriceController::class, 'getPricesForRoomType'])
        ->name('api.roomtype-prices');
    
    // ======================
    // PRICE REQUESTS (CRUD)
    // ======================
    // Submit new request
    Route::post('/api/request', [AdminServicePriceController::class, 'requestDefaultPriceChange'])
        ->name('api.request-price-change');
    Route::put('/api/requests/{id}/update', [AdminServicePriceController::class, 'updateRequest'])
        ->name('pricing.requests.update');
    Route::delete('/api/requests/{id}/delete', [AdminServicePriceController::class, 'deleteRequest'])
        ->name('pricing.requests.delete');
    
    // View requests
    Route::get('/api/requests/pending', [AdminServicePriceController::class, 'getPendingRequests'])
        ->name('api.pending-requests');
    
    Route::get('/api/requests/history', [AdminServicePriceController::class, 'getRequestHistory'])
        ->name('api.request-history');
    
    // Manage requests
    Route::put('/api/requests/{id}/approve', [AdminServicePriceController::class, 'approveRequest'])
        ->name('api.approve-request');
    
    Route::put('/api/requests/{id}/reject', [AdminServicePriceController::class, 'rejectRequest'])
        ->name('api.reject-request');
    
    Route::delete('/api/requests/{id}/cancel', [AdminServicePriceController::class, 'cancelRequest'])
        ->name('api.cancel-request');
    
    // ======================
    // ROOM-SPECIFIC PRICES
    // ======================
    Route::post('/api/room-specific', [AdminServicePriceController::class, 'createRoomPrice'])
        ->name('api.create-room-price');
    
    Route::put('/api/room-specific/{id}', [AdminServicePriceController::class, 'updateRoomPrice'])
        ->name('api.update-room-price');
    
    Route::delete('/api/room-specific/{id}', [AdminServicePriceController::class, 'deleteRoomPrice'])
        ->name('api.delete-room-price');
    
    // ======================
    // DROPDOWN DATA (FOR FORMS)
    // ======================
    Route::get('/api/service-categories', [AdminServicePriceController::class, 'getServiceCategories'])
    ->name('api.service-categories');
    
    Route::get('/api/rooms', function() {
        $rooms = \App\Models\Room::where('status', 'active')->get(['id', 'name']);
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    })->name('api.rooms');
    
    Route::get('/api/room-types', function() {
        $types = \App\Models\RoomType::where('status', 'active')->get(['id', 'name']);
        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    })->name('api.room-types');
    
    
    // ======================
    // BULK OPERATIONS
    // ======================
    Route::post('/api/bulk-update', [AdminServicePriceController::class, 'bulkUpdatePrices'])
        ->name('api.bulk-update');
    
    Route::post('/api/apply-to-all-rooms', [AdminServicePriceController::class, 'applyToAllRooms'])
        ->name('api.apply-to-all-rooms');
});

// Dashboard API routes
Route::get('/dashboard/transactions', [BookingController::class, 'getTransactions'])->name('dashboard.transactions');
Route::get('/dashboard/chart-data', [BookingController::class, 'getChartData'])->name('dashboard.chart-data');

