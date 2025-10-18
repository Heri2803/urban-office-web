<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\MitraPanel\DashboardController;
use App\Http\Controllers\Backend\MitraPanel\HistoryPromoController;
use App\Http\Controllers\Backend\MitraPanel\SettingsController;


Route::get('/mitrapanel/dashboard', [DashboardController::class, 'index'])->name('mitrapanel.dashboard');
Route::get('/layanan/upload', [LayananController::class, 'upload'])->name('mitrapanel.layanan.upload');
Route::post('/layanan/upload', [LayananController::class, 'storeUpload'])->name('mitrapanel.layanan.store');
Route::get('/faktur', [FakturController::class, 'index'])->name('mitrapanel.faktur');
Route::get('/faktur/download/{location}/{year}', [FakturController::class, 'download'])->name('mitrapanel.faktur.download');
Route::get('/settings', [SettingsController::class, 'index'])->name('mitrapanel.settings');
Route::post('/settings', [SettingsController::class, 'update'])->name('mitrapanel.settings.update');
Route::get('/mitrapanel/lokasi/{slug}', function($slug) {
        // Map slug ke nama lokasi
        $locationNames = [
            'jakarta-pusat' => 'Urban Office Jakarta Pusat',
            'surabaya' => 'Urban Office Surabaya',
            'bandung' => 'Urban Office Bandung'
        ];
        
        return view('layouts.mitrapanel.lokasi-detail', [
            'locationSlug' => $slug,
            'locationName' => $locationNames[$slug] ?? 'Lokasi Tidak Ditemukan'
        ]);
    })->name('mitrapanel.lokasi.detail');

    
Route::get('/mitrapanel/history-promo', [HistoryPromoController::class, 'index'])
    ->name('mitrapanel.history-promo');

Route::get('/mitrapanel/faktur', function() {return view('layouts.mitrapanel.faktur-pajak');})
    ->name('mitrapanel.faktur');
    

