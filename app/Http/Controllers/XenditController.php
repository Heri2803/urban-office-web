<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class XenditController extends Controller
{
    public function createInvoice(Request $request)
    {
        // Log request awal untuk debug
        Log::info('Xendit createInvoice request:', $request->all());

        // Pastikan key ada di env
        $secretKey = env('XENDIT_SECRET_KEY');
        if (empty($secretKey)) {
            Log::error('XENDIT_SECRET_KEY tidak ditemukan di .env');
            return response()->json(['error' => 'Xendit secret key tidak ditemukan'], 500);
        }

        Configuration::setXenditKey($secretKey);

        $apiInstance = new InvoiceApi();
        $external_id = 'invoice-' . uniqid();
        $amount = $request->amount;
        $payer_email = $request->email ?? 'user@example.com';
        $description = 'Pembayaran Urban Office';

        try {
            // Log sebelum kirim ke Xendit
            Log::info('Mengirim request ke Xendit API', [
                'external_id' => $external_id,
                'amount' => $amount,
                'payer_email' => $payer_email,
                'description' => $description,
            ]);

            // Panggil API Xendit
            $result = $apiInstance->createInvoice([
                'external_id' => $external_id,
                'amount' => $amount,
                'payer_email' => $payer_email,
                'description' => $description,
                'locale' => 'id', // <-- ini yang penting
            ]);

            return response()->json($result);

            // Log hasil sukses
            Log::info('Invoice berhasil dibuat di Xendit:', (array) $result);

            return response()->json([
                'invoice_url' => $result['invoice_url'] ?? null,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            // Log error detail
            Log::error('Gagal membuat invoice Xendit:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}
