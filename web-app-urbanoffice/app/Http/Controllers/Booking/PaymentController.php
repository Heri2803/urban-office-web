<?php

namespace App\Http\Controllers\Booking; // Ganti dengan namespace controller Anda yang sebenarnya

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Fungsi untuk menangani return dari Midtrans

public function handleReturn(Request $request)
{
    // Midtrans akan memberikan order_id dan status di query string
    $orderId = $request->get('order_id');
    $status = $request->get('transaction_status'); // atau menggunakan parameter yang Anda inginkan

    // Jika user sudah login, redirect ke halaman mails/dashboard
    if (Auth::check()) {
        // Redirrect ke halaman mails (memastikan sesi Auth sudah terbentuk)
        return redirect()->route('dashboard.mails')
                         ->with('midtrans_status', $status)
                         ->with('order_id', $orderId);
    }

    // Jika user belum login (kasus aneh, tapi aman), redirect ke login
    return redirect()->route('login');
}
}