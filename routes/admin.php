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
use App\Http\Controllers\Backend\Admin\BonusManagementController;
use App\Http\Controllers\Backend\Admin\BonusClaimController;
use App\Http\Controllers\Backend\Admin\VirtualOfficeController;
use App\Http\Controllers\Backend\Admin\InvoiceController;
use App\Http\Controllers\Backend\Admin\ContractController;
use App\Http\Controllers\Backend\Admin\PromoUsageController;
use App\Http\Controllers\Backend\Admin\SuratController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [BookingController::class, 'getDashboardStats'])->name('dashboard.stats');
    Route::get('/dashboard/chart-data', [BookingController::class, 'getChartData'])->name('dashboard.chart-data');

    Route::get('/messages', function () {
        return view('layouts.admin.message-page');
    })->name('messages');

    // Messaging Routes
    Route::prefix('/messages')->middleware(['auth'])->group(function () {
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

    // Admin Settings Routes
    Route::prefix('admin/settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'show'])->name('index');
        Route::put('/profile', [AdminSettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AdminSettingsController::class, 'updatePassword'])->name('password.update');
        Route::post('/avatar', [AdminSettingsController::class, 'updateAvatar'])->name('avatar.update');
        Route::delete('/avatar', [AdminSettingsController::class, 'removeAvatar'])->name('avatar.remove');
    });

    Route::get('/settings', [AdminSettingsController::class, 'show'])->name('settings');

    // Booking Routes
    Route::get('/booking/all', function () {
        return view('layouts.admin.booking-all');
    })->name('booking.all');

    Route::get('/booking/room-assignment', [RoomAssignController::class, 'index'])->name('booking.room-assignment');
    Route::get('/booking/service-confirmation', [ServiceConfirmationController::class, 'index'])->name('booking.service-confirmation');
    Route::get('/booking/booking-history', function () {
        return view('layouts.admin.booking-history');
    })->name('booking.history');
    Route::get('/booking/walk-in-booking', function () {
        return view('layouts.admin.walk-in-booking');
    })->name('booking.walk-in-booking');
    Route::get('/booking/existing-customer', [ExistingCustomerBookingController::class, 'index'])->name('booking.existing-customer');

    // Booking API Routes Group
    Route::prefix('booking')->name('admin.booking.')->group(function () {
        
        // Room Assignment API
        Route::get('/room-assignment/api/rooms-status', [RoomAssignController::class, 'getRoomsWithStatus'])->name('api.rooms-status');
        Route::get('/room-assignment/api/available-customers', [RoomAssignController::class, 'getAvailableCustomers'])->name('api.available-customers');

        // Existing Customer API Routes
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
            Route::get('/api/export-pdf', [BookingController::class, 'exportPdf'])->name('api.export-pdf');
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
    })->name('room-management');

    // Content Management Routes
    Route::get('/service-photos', function () {
        return view('layouts.admin.service-photos');
    })->name('content.service-photos');

    // Service Photos API
    Route::prefix('service-photos')->name('service-photos.')->group(function () {
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/rooms', [ServicePhotoController::class, 'getRooms'])->name('rooms');
            Route::get('/locations', [ServicePhotoController::class, 'getLocations'])->name('locations');
            Route::get('/room-types', [ServicePhotoController::class, 'getRoomTypes'])->name('room-types');
            Route::get('/photos', [ServicePhotoController::class, 'index'])->name('photos');
            Route::post('/upload', [ServicePhotoController::class, 'store'])->name('upload');
            Route::post('/bulk-upload', [ServicePhotoController::class, 'bulkUpload'])->name('bulk-upload');
            Route::put('/{servicePhoto}', [ServicePhotoController::class, 'update'])->name('update');
            Route::put('/{servicePhoto}/set-primary', [ServicePhotoController::class, 'setPrimary'])->name('set-primary');
            Route::delete('/{servicePhoto}', [ServicePhotoController::class, 'destroy'])->name('destroy');
        });
    });

    // Highlights API
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

    // Banner Promo Management
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
        Route::get('/api/customers', [BannerController::class, 'getCustomers'])->name('customers');
    });

    // ============================================
    // ✅ SURAT MASUK MANAGEMENT
    // ============================================
    Route::prefix('surats')->name('surats.')->group(function () {
        Route::get('/', [SuratController::class, 'index'])->name('index');
        Route::get('/create', [SuratController::class, 'create'])->name('create');
        Route::post('/', [SuratController::class, 'store'])->name('store');
        Route::get('/{surat}', [SuratController::class, 'show'])->name('show');
        Route::get('/{surat}/edit', [SuratController::class, 'edit'])->name('edit');
        Route::put('/{surat}', [SuratController::class, 'update'])->name('update');
        Route::delete('/{surat}', [SuratController::class, 'destroy'])->name('destroy');
        
        // Additional surat routes
        Route::get('/{surat}/download', [SuratController::class, 'download'])->name('download');
        Route::get('/{surat}/recipients', [SuratController::class, 'recipients'])->name('recipients');
        Route::post('/{surat}/resend/{user}', [SuratController::class, 'resendNotification'])->name('resend');
    });

    // Room Management API
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
    })->name('content.highlights');

    // Pricing Routes
    Route::get('/pricing', function () {
        return view('layouts.admin.pricing');
    })->name('pricing');

    Route::prefix('pricing')->name('pricing.')->group(function () {
        
        // Dashboard & Statistics
        Route::get('/api/dashboard-stats', [AdminServicePriceController::class, 'dashboardStats'])
            ->name('api.dashboard-stats');
        
        // Price Views (READ ONLY)
        Route::get('/api/default-prices', [AdminServicePriceController::class, 'getDefaultPrices'])
            ->name('api.default-prices');
        
        Route::get('/api/default-prices/{categorySlug}', [AdminServicePriceController::class, 'getCategoryDefaultPrices'])
            ->name('api.category-default-prices');
        
        Route::get('/api/prices-by-roomtype', [AdminServicePriceController::class, 'getPricesByRoomType'])
            ->name('api.prices-by-roomtype');
        
        Route::get('/api/roomtype/{roomTypeId}/prices', [AdminServicePriceController::class, 'getPricesForRoomType'])
            ->name('api.roomtype-prices');
        
        // Price Requests (CRUD)
        Route::post('/api/request', [AdminServicePriceController::class, 'requestDefaultPriceChange'])
            ->name('api.request-price-change');
        Route::put('/api/requests/{id}/update', [AdminServicePriceController::class, 'updateRequest'])
            ->name('pricing.requests.update');
        Route::delete('/api/requests/{id}/delete', [AdminServicePriceController::class, 'deleteRequest'])
            ->name('pricing.requests.delete');
        
        Route::get('/api/requests/pending', [AdminServicePriceController::class, 'getPendingRequests'])
            ->name('api.pending-requests');
        
        Route::get('/api/requests/history', [AdminServicePriceController::class, 'getRequestHistory'])
            ->name('api.request-history');
        
        Route::put('/api/requests/{id}/approve', [AdminServicePriceController::class, 'approveRequest'])
            ->name('api.approve-request');
        
        Route::put('/api/requests/{id}/reject', [AdminServicePriceController::class, 'rejectRequest'])
            ->name('api.reject-request');
        
        Route::delete('/api/requests/{id}/cancel', [AdminServicePriceController::class, 'cancelRequest'])
            ->name('api.cancel-request');
        
        // Room-Specific Prices
        Route::post('/api/room-specific', [AdminServicePriceController::class, 'createRoomPrice'])
            ->name('api.create-room-price');
        
        Route::put('/api/room-specific/{id}', [AdminServicePriceController::class, 'updateRoomPrice'])
            ->name('api.update-room-price');
        
        Route::delete('/api/room-specific/{id}', [AdminServicePriceController::class, 'deleteRoomPrice'])
            ->name('api.delete-room-price');
        
        // Dropdown Data (For Forms)
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
        
        // Bulk Operations
        Route::post('/api/bulk-update', [AdminServicePriceController::class, 'bulkUpdatePrices'])
            ->name('api.bulk-update');
        
        Route::post('/api/apply-to-all-rooms', [AdminServicePriceController::class, 'applyToAllRooms'])
            ->name('api.apply-to-all-rooms');
    });

    // Bonus Management & Claim
    Route::prefix('bonus')->name('bonus.')->group(function () {
        
        // Bonus Rules CRUD
        Route::prefix('rules')->name('rules.')->group(function () {
            Route::get('/', [BonusManagementController::class, 'index'])->name('index');
            Route::post('/', [BonusManagementController::class, 'store'])->name('store');
            Route::get('/{id}', [BonusManagementController::class, 'show'])->name('show');
            Route::put('/{id}', [BonusManagementController::class, 'update'])->name('update');
            Route::delete('/{id}', [BonusManagementController::class, 'destroy'])->name('destroy');
        });
        
        // Manual Add Bonus
        Route::post('/manual-add', [BonusManagementController::class, 'manualAddBonus'])
            ->name('manual-add');
        
        // Customer Bonus List
        Route::get('/customers/{user_id}', [BonusClaimController::class, 'customerBonuses'])
            ->name('customers.bonuses');

        Route::get('/customers-with-active-bonus', [BonusClaimController::class, 'customersWithActiveBonus'])
            ->name('customers.with-active-bonus');
        
        // Available Rooms Check
        Route::get('/rooms/available', [BonusClaimController::class, 'availableRooms'])
            ->name('rooms.available');
        
        // Claim Bonus
        Route::post('/claim', [BonusClaimController::class, 'claim'])
            ->name('claim');
        
        // Claim History
        Route::get('/claims', [BonusClaimController::class, 'index'])
            ->name('claims.index');
        
        // Cancel Claim
        Route::post('/claims/{id}/cancel', [BonusClaimController::class, 'cancel'])
            ->name('claims.cancel');
    });

    // ============================================
    // VIRTUAL OFFICE MANAGEMENT
    // ============================================
    Route::prefix('virtual-office')->name('virtual-office.')->group(function () {
        Route::get('/', [VirtualOfficeController::class, 'index'])->name('index');
        Route::get('/{id}', [VirtualOfficeController::class, 'show'])->name('show');
        Route::get('/{transactionId}/documents', [VirtualOfficeController::class, 'getDocuments'])->name('documents');
        Route::post('/documents/{documentId}/verify', [VirtualOfficeController::class, 'verifyDocument'])->name('documents.verify');
        Route::get('/documents/{documentId}/download', [VirtualOfficeController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/export/pdf', [VirtualOfficeController::class, 'export'])->name('export');
        Route::post('/import', [VirtualOfficeController::class, 'import'])->name('import');
        Route::get('/import/template', [VirtualOfficeController::class, 'downloadTemplate'])->name('import.template');
    });

    // ============================================
    // ✅ INVOICE MANAGEMENT - TAMBAHKAN DI SINI
    // ============================================
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/create-manual', [InvoiceController::class, 'createManual'])->name('create-manual');
        Route::post('/manual', [InvoiceController::class, 'storeManual'])->name('store-manual');
        
        // Settlement request routes
        Route::post('/{invoice}/request-settlement', [InvoiceController::class, 'requestSettlement'])->name('request-settlement');
        Route::get('/approvals', [InvoiceController::class, 'approvalIndex'])->name('approvals.index');
        Route::post('/approvals/{invoice}/approve', [InvoiceController::class, 'approveSettlement'])->name('approvals.approve');
        Route::post('/approvals/{invoice}/reject', [InvoiceController::class, 'rejectSettlement'])->name('approvals.reject');
        
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
        Route::get('/{invoice}/whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('whatsapp');
        Route::post('/{invoice}/sync-status', [InvoiceController::class, 'syncStatus'])->name('sync-status');
        Route::put('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
    });

    // Contract Management
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])
            ->name('index');

        // Generate PDF (dari draft → active), parameter Transaction
        Route::get('/virtual-office/{transaction}/pdf',
            [ContractController::class, 'generateVirtualOfficeContract'])
            ->name('vo.pdf');

        // Download ulang PDF yang sudah ada di storage
        Route::get('/download/{contract}',
            [ContractController::class, 'downloadContract'])
            ->name('download');

        Route::patch('/{contract}/status', [ContractController::class, 'updateStatus'])->name('update-status');

        // Addendum Routes
        Route::get('/addendums/{addendum}/download', [ContractController::class, 'downloadAddendum'])->name('addendums.download');
        Route::patch('/addendums/{addendum}/terminate', [ContractController::class, 'terminateAddendum'])->name('addendums.terminate');
    });

    // ============================================
    // ADDENDUM MANAGEMENT (Halaman Terpisah)
    // ============================================
    Route::prefix('addendums')->name('addendums.')->group(function () {
        Route::get('/', [ContractController::class, 'addendumIndex'])->name('index');
        Route::get('/{addendum}/download', [ContractController::class, 'downloadAddendum'])->name('download');
        Route::patch('/{addendum}/terminate', [ContractController::class, 'terminateAddendum'])->name('terminate');
    });

    // ============================================
    // PROMO USAGE REPORT
    // ============================================
    Route::prefix('promo-usage')->name('promo-usage.')->group(function () {
        Route::get('/', [PromoUsageController::class, 'index'])->name('index');
        Route::get('/api', [PromoUsageController::class, 'apiIndex'])->name('api');
        Route::get('/stats', [PromoUsageController::class, 'getSummaryStats'])->name('stats');
        Route::get('/export', [PromoUsageController::class, 'exportCsv'])->name('export');
    });

    // Dashboard API routes
    Route::get('/dashboard/transactions', [BookingController::class, 'getTransactions'])->name('dashboard.transactions');
    Route::get('/dashboard/chart-data', [BookingController::class, 'getChartData'])->name('dashboard.chart-data');

});
