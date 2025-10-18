<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Deteksi otomatis HTTPS dari Ngrok
        if ($this->app->environment('local')) {
            // Cek header dari Ngrok
            if (request()->header('X-Forwarded-Proto') === 'https' || 
                request()->header('X-Forwarded-Ssl') === 'on') {
                URL::forceScheme('https');
            }
        }

        // Atau lebih simple (force HTTPS untuk semua kecuali localhost langsung)
        if (!request()->is('localhost*') && !request()->is('127.0.0.1*')) {
            if (isset($_SERVER['HTTPS']) || 
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
                URL::forceScheme('https');
            }
        }
    }
}
