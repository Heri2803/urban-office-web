<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MitraPanel\DashboardController;
use App\Http\Controllers\Backend\MitraPanel\HistoryPromoController;
use App\Http\Controllers\Backend\MitraPanel\SettingsController;
use App\Http\Controllers\Backend\MitraPanel\MitraPanelController;
use App\Http\Controllers\Backend\Mitrapanel\FakturPajakController;
use App\Http\Controllers\Backend\MitraPanel\MitraAccessController;

Route::middleware(['auth', 'mitra.access'])
    ->prefix('mitrapanel')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('mitrapanel.dashboard');

        // Transaksi
        Route::get('/transactions', [MitraAccessController::class, 'getTransactions'])
            ->name('mitrapanel.transactions');

        // Faktur Pajak (Download specific)
        Route::get('/faktur-pajak/{faktur}/download', [FakturPajakController::class, 'downloadPDF'])
            ->name('mitrapanel.faktur-pajak.download');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])
            ->name('mitrapanel.settings');
        Route::post('/settings', [SettingsController::class, 'update'])
            ->name('mitrapanel.settings.update');
        Route::get('/settings/profile-data', [SettingsController::class, 'getProfileData'])
            ->name('mitrapanel.settings.profile-data');
        Route::post('/settings/profile-update', [SettingsController::class, 'updateProfile'])
            ->name('mitrapanel.settings.profile-update');
        Route::post('/settings/password-update', [SettingsController::class, 'updatePassword'])
            ->name('mitrapanel.settings.password-update');
        Route::post('/settings/notifications-update', [SettingsController::class, 'updateNotifications'])
            ->name('mitrapanel.settings.notifications-update');

        // History Promo Page
        Route::get('/history-promo', [HistoryPromoController::class, 'index'])
            ->name('mitrapanel.history-promo');

        // API routes
        Route::prefix('api')->group(function () {
            Route::get('/locations', [MitraPanelController::class, 'getLocations']);
            Route::get('/dashboard-data', [MitraPanelController::class, 'getDashboardData']);
            Route::get('/promo-history', [HistoryPromoController::class, 'getPromoHistory'])
                    ->name('mitrapanel.api.promo-history');

            Route::post('/export/promo-history', [HistoryPromoController::class, 'exportHistory'])
                    ->name('mitrapanel.export.promo-history');
        });

        // Faktur Pajak (full set)
        Route::get('/faktur-pajak', [FakturPajakController::class, 'index'])
            ->name('mitrapanel.faktur-pajak');

        Route::get('/faktur-pajak/download-location/{locationId}', [FakturPajakController::class, 'downloadLocationFaktur'])
            ->name('mitrapanel.faktur-pajak.download-location');

        Route::get('/faktur-pajak/download-all', [FakturPajakController::class, 'downloadAllFaktur'])
            ->name('mitrapanel.faktur-pajak.download-all');

        // Lokasi (API)
        Route::get('/lokasi/get-data', [MitraPanelController::class, 'getData'])
            ->name('mitrapanel.lokasi.getData');

        Route::post('/lokasi/report-tax', [MitraPanelController::class, 'reportTax'])
            ->name('mitrapanel.lokasi.reportTax');

        Route::get('/lokasi/export-pdf', [MitraPanelController::class, 'exportPDF'])
            ->name('mitrapanel.lokasi.exportPDF');

        Route::get('/lokasi/export-excel', [MitraPanelController::class, 'exportExcel'])
            ->name('mitrapanel.lokasi.exportExcel');

        Route::get('/lokasi/export-service-detail', [MitraPanelController::class, 'exportServiceDetail'])
            ->name('mitrapanel.lokasi.exportServiceDetail');

        // Slug route — wajib paling bawah
        Route::get('/lokasi/{slug}', [MitraPanelController::class, 'index'])
            ->name('mitrapanel.lokasi.detail');
    });
