<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Transaction;
use App\Observers\TransactionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // =========================================================
        // OBSERVER REGISTRATION
        // =========================================================

        // Auto-generate invoice saat transaksi baru dibuat
        Transaction::observe(TransactionObserver::class);

        // =========================================================
        // HTTPS / NGROK DETECTION
        // =========================================================

        // Deteksi otomatis HTTPS dari Ngrok
        if ($this->app->environment('local')) {
            if (request()->header('X-Forwarded-Proto') === 'https' ||
                request()->header('X-Forwarded-Ssl') === 'on') {
                URL::forceScheme('https');
            }
        }

        // Force HTTPS untuk semua kecuali localhost langsung
        if (!request()->is('localhost*') && !request()->is('127.0.0.1*')) {
            if (isset($_SERVER['HTTPS']) ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
                URL::forceScheme('https');
            }
        }
    }
}