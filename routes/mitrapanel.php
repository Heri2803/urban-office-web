<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MitraPanel\DashboardController;
use App\Http\Controllers\Backend\MitraPanel\HistoryPromoController;
use App\Http\Controllers\Backend\MitraPanel\SettingsController;
use App\Http\Controllers\Backend\MitraPanel\MitraPanelController;
use App\Http\Controllers\Backend\Mitrapanel\FakturPajakController;
use App\Http\Controllers\Backend\MitraPanel\MitraAccessController;

Route::get('/mitrapanel/dashboard', [DashboardController::class, 'index'])->name('mitrapanel.dashboard');

Route::get('/mitrapanel/transactions', [MitraAccessController::class, 'getTransactions'])
    ->name('mitrapanel.transactions');

Route::get('/mitrapanel/faktur-pajak/{faktur}/download', [FakturPajakController::class, 'downloadPDF'])
    ->name('mitrapanel.faktur-pajak.download');

Route::get('/settings', [SettingsController::class, 'index'])->name('mitrapanel.settings');
Route::post('/settings', [SettingsController::class, 'update'])->name('mitrapanel.settings.update');
Route::get('/settings/profile-data', [SettingsController::class, 'getProfileData'])->name('mitrapanel.settings.profile-data');
Route::post('/settings/profile-update', [SettingsController::class, 'updateProfile'])->name('mitrapanel.settings.profile-update');
Route::post('/settings/password-update', [SettingsController::class, 'updatePassword'])->name('mitrapanel.settings.password-update');
Route::post('/settings/notifications-update', [SettingsController::class, 'updateNotifications'])->name('mitrapanel.settings.notifications-update');

// ✅ CORRECTED: PINDAHKAN SEMUA API ROUTES KE DALAM MITRAPANEL GROUP
Route::prefix('mitrapanel')->group(function () {

    // History Promo Page
    Route::get('/history-promo', [HistoryPromoController::class, 'index'])
        ->name('mitrapanel.history-promo');

    // ✅ API ROUTES - SEKARANG DI DALAM MITRAPANEL PREFIX
    Route::prefix('api')->group(function () {
        Route::get('/locations', [MitraPanelController::class, 'getLocations']);
        Route::get('/dashboard-data', [MitraPanelController::class, 'getDashboardData']);
        Route::get('/promo-history', [HistoryPromoController::class, 'getPromoHistory'])
                ->name('mitrapanel.api.promo-history');
                
        Route::post('/export/promo-history', [HistoryPromoController::class, 'exportHistory'])
                ->name('mitrapanel.export.promo-history');
    });

    // Existing routes...
    Route::get('/faktur-pajak', [FakturPajakController::class, 'index'])
        ->name('mitrapanel.faktur-pajak');
    
        // Download semua faktur per lokasi
    Route::get('/faktur-pajak/download-location/{locationId}', [FakturPajakController::class, 'downloadLocationFaktur'])
        ->name('mitrapanel.faktur-pajak.download-location');

    // Download semua faktur semua lokasi
    Route::get('/faktur-pajak/download-all', [FakturPajakController::class, 'downloadAllFaktur'])
        ->name('mitrapanel.faktur-pajak.download-all');
    
    // ✅ API GET DATA
    Route::get('/lokasi/get-data', [MitraPanelController::class, 'getData'])
        ->name('mitrapanel.lokasi.getData');

    // ✅ API REPORT TAX
    Route::post('/lokasi/report-tax', [MitraPanelController::class, 'reportTax'])
        ->name('mitrapanel.lokasi.reportTax');

    // ✅ API EXPORT PDF
    Route::get('/lokasi/export-pdf', [MitraPanelController::class, 'exportPDF'])
        ->name('mitrapanel.lokasi.exportPDF');

    // ✅ API EXPORT EXCEL
    Route::get('/lokasi/export-excel', [MitraPanelController::class, 'exportExcel'])
        ->name('mitrapanel.lokasi.exportExcel');

    // ✅ API EXPORT SERVICE DETAIL
    Route::get('/lokasi/export-service-detail', [MitraPanelController::class, 'exportServiceDetail'])
        ->name('mitrapanel.lokasi.exportServiceDetail');

    // ⚠️ Route dengan {slug} HARUS di paling bawah!
    Route::get('/lokasi/{slug}', [MitraPanelController::class, 'index'])
        ->name('mitrapanel.lokasi.detail');
});