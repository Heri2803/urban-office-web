<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // Halaman daftar invoice
    public function index()
    {
        $transactions = Transaction::latest()->get();

        // Kirim ke view daftar invoice
        return view('layouts.dashboard.invoice', compact('transactions'));
    }

    // Generate & download PDF untuk transaksi tertentu
    public function generate($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        $pdf = Pdf::loadView('layouts.invoices.pdf', compact('transaction'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$transaction->order_id}.pdf");
    }
}
