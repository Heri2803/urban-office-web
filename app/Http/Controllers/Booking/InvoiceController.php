<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Cari invoice berdasarkan invoice_number (untuk customer profile page).
     * Hanya menampilkan invoice milik user yang sedang login.
     */
    public function searchByNumber(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|min:3',
        ]);

        $invoiceNumber = trim($request->input('invoice_number'));

        $invoice = Invoice::with([
                'transaction.location',
                'transaction.room',
                'transaction.serviceCategory',
                'creator',
            ])
            ->where('invoice_number', $invoiceNumber)
            ->whereHas('transaction', function ($q) {
                // Batasi hanya invoice milik user yang login
                $q->where('user_id', Auth::id())
                  ->orWhere('email', Auth::user()->email);
            })
            ->first();  

        if (!$invoice) {
            return response()->json([
                'found'   => false,
                'message' => 'Invoice tidak ditemukan. Pastikan nomor invoice sudah benar.',
            ]);
        }

        $transaction = $invoice->transaction;

        return response()->json([
            'found' => true,
            'data'  => [
                // --- Invoice ---
                'invoice_number'  => $invoice->invoice_number,
                'status'          => $invoice->status,
                'created_by_name' => $invoice->creator_name,
                'formatted_date'  => $invoice->formatted_date,
                'formatted_total' => $invoice->formatted_total,

                // --- Transaksi ---
                'order_id'        => $transaction->order_id,
                'nama_lengkap'    => $transaction->nama_lengkap,
                'email'           => $transaction->email,
                'phone'           => $transaction->phone,
                'company_name'    => $transaction->company_name ?? '-',
                'room_type'       => $transaction->room_type,
                'paket'           => $transaction->paket ?? '-',
                'booking_date'    => $transaction->booking_date
                                        ? $transaction->booking_date->format('d F Y')
                                        : '-',
                'duration_text'   => $transaction->duration_text,
                'participants_text'=> $transaction->participants_text,
                'location_name'   => $transaction->location->name ?? '-',
                'room_name'       => $transaction->room->name ?? '-',
                'gross_amount'    => 'Rp ' . number_format($transaction->gross_amount, 0, ',', '.'),
            ],
        ]);
    }

    /**
     * Halaman daftar invoice milik user yang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Menggabungkan transaksi milik user_id ini ATAU memiliki email yang sama dgn customer
        $transactions = Transaction::where(function ($query) use ($user) {
                                $query->where('user_id', $user->id)
                                      ->orWhere('email', $user->email);
                            })
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Map data agar payload JSON untuk Alpine.js lebih ringan dan aman (hanya load kolom spesifik)
        $mappedTransactions = $transactions->map(function ($transaction) {
            return [
                'order_id'     => $transaction->order_id,
                'room_type'    => $transaction->room_type,
                'nama_lengkap' => $transaction->nama_lengkap,
                'created_at'   => $transaction->created_at,
                'status'       => $transaction->status, // pending, settlement, expire, dll
                'gross_amount' => $transaction->gross_amount, 
                'total_amount' => $transaction->total_amount // termasuk biaya lunch jika ada
            ];
        });

        return view('layouts.dashboard.invoice', [
            'transactions' => $mappedTransactions
        ]);
    }

    /**
     * Generate & download PDF invoice untuk transaksi tertentu.
     */
    public function generate($order_id)
    {
        // ========== LOAD TRANSACTION DENGAN EAGER LOADING ==========
        $transaction = Transaction::with([
            'location',
            'city',
            'serviceCategory',
            'room',
            'lunches.lunchOption',
            'invoice',  // load invoice untuk tampilkan invoice_number di PDF
        ])->where('order_id', $order_id)->firstOrFail();

        // Ambil invoice jika sudah ada (auto-generated oleh observer)
        $invoice = $transaction->invoice;

        // ========== HITUNG LUNCH TOTAL ==========
        $lunchTotal = $transaction->calculateLunchTotal();

        // Update lunch_total di object jika berbeda (tanpa save ke DB)
        if ($lunchTotal != $transaction->lunch_total) {
            $transaction->lunch_total = $lunchTotal;
        }

        // ========== HITUNG BIAYA SEWA (TANPA DEPOSIT DAN LUNCH) ==========
        $sewaAmount = $transaction->gross_amount - ($transaction->deposit ?? 0) - $transaction->lunch_total;
        $sewaAmount = max(0, $sewaAmount);

        // ========== HITUNG TOTAL AMOUNT ==========
        $totalAmount = $sewaAmount + $transaction->lunch_total + ($transaction->deposit ?? 0);

        // ========== BACKGROUND INVOICE ==========
        // Prioritas: path relatif (cross-platform) → localhost Windows → production Linux
        $backgroundPath = base_path('public/assets/Invoice Virtual Office Mentahan.png');

        if (!File::exists($backgroundPath)) {
            // Localhost Windows (Laragon)
            $backgroundPath = 'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\Invoice Virtual Office Mentahan.png';
        }

        if (!File::exists($backgroundPath)) {
            // Production Linux
            $backgroundPath = '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/Invoice Virtual Office Mentahan.png';
        }

        $backgroundImage = null;
        if (File::exists($backgroundPath)) {
            try {
                $imageData = File::get($backgroundPath);
                $backgroundImage = 'data:image/png;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Failed to load invoice background: ' . $e->getMessage());
                $backgroundImage = '';
            }
        }

        // ========== TTD SIGNATURE ==========
        // Prioritas: path relatif (cross-platform) → localhost Windows → production Linux
        // Catatan: nama file berbeda antara localhost dan production
        $ttdPath = base_path('public/assets/TTD PAK MEGA.jpeg'); // localhost filename

        if (!File::exists($ttdPath)) {
            $ttdPath = base_path('public/assets/ttd_pak_mega.jpeg'); // production filename
        }

        if (!File::exists($ttdPath)) {
            // Localhost Windows (Laragon)
            $ttdPath = 'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\TTD PAK MEGA.jpeg';
        }

        if (!File::exists($ttdPath)) {
            // Production Linux
            $ttdPath = '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/ttd_pak_mega.jpeg';
        }

        $ttdImage = null;
        if (File::exists($ttdPath)) {
            try {
                $imageData = File::get($ttdPath);
                $ttdImage = 'data:image/jpeg;base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
                \Log::error('Failed to load TTD image: ' . $e->getMessage());
                $ttdImage = '';
            }
        }

        // ========== GENERATE PDF ==========
        $pdf = Pdf::loadView('layouts.invoices.pdf', [
            'invoice'         => $invoice,       // bisa null jika invoice belum ada
            'transaction'     => $transaction,
            'backgroundImage' => $backgroundImage,
            'hasBackground'   => !empty($backgroundImage),
            'ttdImage'        => $ttdImage,
            'hasTTD'          => !empty($ttdImage),
            'lunchTotal'      => $lunchTotal,
            'sewaAmount'      => $sewaAmount,
            'totalAmount'     => $totalAmount,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$transaction->order_id}.pdf");
    }
}