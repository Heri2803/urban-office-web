<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'midtrans/notification',
         '/payment/*',// Tambahkan route notification Midtrans
         'pricing/api/request', // TAMBAHKAN INI
        'pricing/api/*', // atau gunakan wildcard untuk semua route di pricing/api
    ];
}   
