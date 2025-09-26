<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Dapatkan dari dashboard Midtrans (Access Key)
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk Snap JS di frontend
    |
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Environment
    |--------------------------------------------------------------------------
    |
    | Pilih true jika production, false jika sandbox
    |
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
];
