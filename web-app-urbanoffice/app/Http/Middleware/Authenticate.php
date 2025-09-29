<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
{
    if (! $request->expectsJson()) {
        // Cek jika ada parameter dari Midtrans
        if ($request->has('order_id') || $request->has('transaction_status')) {
            return null; // Jangan redirect ke login
        }
        
        return route('login');
    }
}

}
