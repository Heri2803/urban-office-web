<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoicePdfService
{
    /**
     * Generate PDF invoice, simpan ke storage/public, dan return URL publik.
     * Logic ini di-extract dari InvoiceController@generate agar bisa
     * dipanggil juga dari MidtransWebhookController.
     */
    public function generateAndStore(Transaction $transaction): string
    {
        // ── Load relasi yang dibutuhkan ──
        $transaction->loadMissing([
            'location',
            'city',
            'serviceCategory',
            'room',
            'lunches.lunchOption',
            'invoice',
        ]);

        $invoice    = $transaction->invoice;
        $lunchTotal = $transaction->calculateLunchTotal();

        if ($lunchTotal != $transaction->lunch_total) {
            $transaction->lunch_total = $lunchTotal;
        }

        $sewaAmount  = max(0, $transaction->gross_amount - ($transaction->deposit ?? 0) - $transaction->lunch_total);
        $totalAmount = $sewaAmount + $transaction->lunch_total + ($transaction->deposit ?? 0);

        // ── Load background image (sama persis dengan InvoiceController) ──
        $backgroundImage = $this->loadImageAsBase64([
            base_path('public/assets/Invoice Virtual Office Mentahan.png'),
            'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\Invoice Virtual Office Mentahan.png',
            '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/Invoice Virtual Office Mentahan.png',
        ], 'png');

        // ── Load TTD image ──
        $ttdImage = $this->loadImageAsBase64([
            base_path('public/assets/TTD PAK MEGA.jpeg'),
            base_path('public/assets/ttd_pak_mega.jpeg'),
            'D:\laragon\www\webappurban\web-app-urbanoffice\public\assets\TTD PAK MEGA.jpeg',
            '/home/K7308095/webappurban/web-app-urbanoffice/public/assets/ttd_pak_mega.jpeg',
        ], 'jpeg');

        // ── Generate PDF dengan view yang sama seperti InvoiceController ──
        $pdf = Pdf::loadView('layouts.invoices.pdf', [
            'invoice'         => $invoice,
            'transaction'     => $transaction,
            'backgroundImage' => $backgroundImage,
            'hasBackground'   => !empty($backgroundImage),
            'ttdImage'        => $ttdImage,
            'hasTTD'          => !empty($ttdImage),
            'lunchTotal'      => $lunchTotal,
            'sewaAmount'      => $sewaAmount,
            'totalAmount'     => $totalAmount,
        ])->setPaper('a4', 'portrait');

        // ── Simpan ke storage/public/invoices/ ──
        $filename = 'invoices/Invoice-' . $transaction->order_id . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        Log::info('[InvoicePdfService] PDF disimpan ke storage', [
            'order_id' => $transaction->order_id,
            'path'     => $filename,
        ]);

        // Return URL publik yang bisa diakses customer
        return Storage::disk('public')->url($filename);
    }

    /**
     * Helper: load gambar dari beberapa path alternatif, return base64.
     */
    private function loadImageAsBase64(array $paths, string $mime): ?string
    {
        foreach ($paths as $path) {
            if (File::exists($path)) {
                try {
                    $data = File::get($path);
                    return "data:image/{$mime};base64," . base64_encode($data);
                } catch (\Exception $e) {
                    Log::warning('[InvoicePdfService] Gagal load image: ' . $e->getMessage());
                }
            }
        }
        return null;
    }
}