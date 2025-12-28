<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Superadmin\DashboardController;
use App\Http\Controllers\Backend\Superadmin\PricingApprovalController;
use App\Http\Controllers\Backend\Superadmin\PricingOverrideController;

// ====================================================
// SUPERADMIN ROUTES - SEMUA DILINDUNGI MIDDLEWARE
// ====================================================
Route::prefix('superadmin')
    ->middleware(['auth', 'superadmin']) // ✅ MIDDLEWARE APPLIED
    ->name('superadmin.')
    ->group(function () {
    
    // ================================================
    // DASHBOARD
    // ================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ================================================
    // MITRA MANAGEMENT
    // ================================================
    Route::get('/mitra/all', function () {
        return view('layouts.superadmin.mitra-all');
    })->name('mitra.all');
    
    Route::get('/mitra/revenue-mitra', function () {
        return view('layouts.superadmin.revenue-share-mitra');
    })->name('mitra.revenue-mitra');
    
    // ================================================
    // BRANCH MANAGEMENT
    // ================================================
    Route::get('/branches/all', function () {
        return view('layouts.superadmin.branches-all');
    })->name('branches.all');
    
    Route::get('/branches/settings', function () {
        return view('layouts.superadmin.branches-settings');
    })->name('branches.settings');
    
    // ================================================
    // USER MANAGEMENT
    // ================================================
    Route::get('/users', function () {
        return view('superadmin.users');
    })->name('users');
    
    Route::get('/user-management/user-manage-all', function () {
        return view('layouts.superadmin.user-manage-all');
    })->name('user-management.user-manage-all');
    
    Route::get('/user-management/user-activitylog-manage', function () {
        return view('layouts.superadmin.user-activitylog-manage');
    })->name('user-management.user-activitylog-manage');
    
    // ================================================
    // BOOKING MANAGEMENT
    // ================================================
    Route::get('/booking/reports', function () {
        return view('layouts.superadmin.booking-reports');
    })->name('booking.reports');
    
    Route::get('/booking/all', function () {
        return view('layouts.superadmin.booking-all');
    })->name('booking.all');
    
    // ================================================
    // ROOM MANAGEMENT
    // ================================================
    Route::get('/room-management/all', function () {
        return view('layouts.superadmin.room-manage-all');
    })->name('room-management.all');
    
    // ================================================
    // CONTENT MANAGEMENT
    // ================================================
    Route::get('/content-management/photos', function () {
        return view('layouts.superadmin.content-photos');
    })->name('content-management.photos');
    
    Route::get('/content-management/banner', function () {
        return view('layouts.superadmin.content-banner');
    })->name('content-management.banner');
    
    Route::get('/content-management/highlight', function () {
        return view('layouts.superadmin.content-highlight');
    })->name('content-management.highlight');
    
    // ================================================
    // PRICING MANAGEMENT
    // ================================================
    Route::get('/pricing-management/master', function () {
        return view('layouts.superadmin.pricing-master');
    })->name('pricing-management.master');
    
    Route::get('/pricing-management/branch', function () {
        return view('layouts.superadmin.pricing-branch');
    })->name('pricing-management.branch');
    
    // ⭐ Halaman BARU: Pricing Approval
    Route::get('/pricing-management/approval', function () {
        return view('layouts.superadmin.pricing-approval');
    })->name('pricing-management.approval');
    
    // ================================================
    // VOUCHER & PROMO
    // ================================================
    Route::get('/voucher-promo/create', function () {
        return view('layouts.superadmin.voucher-promo-create');
    })->name('voucher-promo.create');
    
    Route::get('/voucher-promo/active', function () {
        return view('layouts.superadmin.voucher-promo-active');
    })->name('voucher-promo.active');
    
    Route::get('/voucher-promo/usage', function () {
        return view('layouts.superadmin.voucher-promo-usage');
    })->name('voucher-promo.usage');
    
    // ================================================
    // REPORTS
    // ================================================
    Route::get('/reports/revenue', function () {
        return view('layouts.superadmin.report-revenue');
    })->name('reports.revenue');
    
    Route::get('/reports/occupancy', function () {
        return view('layouts.superadmin.report-occupancy');
    })->name('reports.occupancy');
    
    // ================================================
    // APPROVAL (GENERAL)
    // ================================================
    Route::get('/approval/pending', function () {
        return view('layouts.superadmin.approval-pending');
    })->name('approval.pending');
    
    // ================================================
    // PROFILE
    // ================================================
    Route::get('/profile', function () {
        return view('superadmin.profile');
    })->name('profile');
    
    // ================================================
    // API ROUTES untuk Alpine.js/Fetch
    // Semua route di bawah ini return JSON (harus auth + superadmin)
    // ================================================
    Route::prefix('api')->name('api.')->group(function () {
        
        // ⭐ PRICING APPROVAL API ROUTES
        Route::prefix('pricing')->name('pricing.')->group(function () {
            // Endpoint untuk semua request dengan filter status
            Route::get('/requests', [PricingApprovalController::class, 'getRequestsByStatus'])
                ->name('requests.by-status');
                
            // GET: /superadmin/api/pricing/pending
            Route::get('/pending', [PricingApprovalController::class, 'getPendingRequests'])
                ->name('pending');
            
            // POST: /superadmin/api/pricing/{id}/approve
            Route::post('/{id}/approve', [PricingApprovalController::class, 'approveRequest'])
                ->name('approve');
            
            // POST: /superadmin/api/pricing/{id}/reject
            Route::post('/{id}/reject', [PricingApprovalController::class, 'rejectRequest'])
                ->name('reject');
            
            // GET: /superadmin/api/pricing/{id}/details
            Route::get('/{id}/details', [PricingApprovalController::class, 'getRequestDetails'])
                ->name('details');

            Route::get('/filter-options', [PricingApprovalController::class, 'getFilterOptions']);
        });

        Route::prefix('generate')->name('generate.')->group(function () {
                    // GET: /superadmin/api/pricing/generate/form-data
                    Route::get('/form-data', [pricingOverrideController::class, 'getFormData'])
                        ->name('form-data');
                    
                    // POST: /superadmin/api/pricing/generate/submit
                    Route::post('/submit', [pricingOverrideController::class, 'submitRequest'])
                        ->name('submit');

                    Route::put('/{id}/edit', [PricingOverrideController::class, 'editActivePrice'])
                        ->name('edit');
                    
                    // GET: /superadmin/api/pricing/generate/my-requests
                    Route::get('/my-requests', [pricingOverrideController::class, 'getMyRequests'])
                        ->name('my-requests');
                    
                    Route::delete('/{id}/soft-delete', [PricingOverrideController::class, 'softDeletePrice'])
                        ->name('soft-delete');
                        
                    Route::post('/{id}/restore', [PricingOverrideController::class, 'restorePrice'])
                        ->name('restore');
                        
                    Route::post('/{id}/force-delete', [PricingOverrideController::class, 'forceDeletePrice'])
                        ->name('force-delete');
                        
                    Route::get('/trash', [PricingOverrideController::class, 'getTrash'])
                        ->name('trash.index');

            });
        
        // NOTE: Bisa tambah API routes lainnya di sini nanti
        // Route::prefix('booking')->name('booking.')->group(function () { ... });
        // Route::prefix('reports')->name('reports.')->group(function () { ... });
    });
});