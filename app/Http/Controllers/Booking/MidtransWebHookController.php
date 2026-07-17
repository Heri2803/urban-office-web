<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use App\Services\BalesOtomatisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MidtransWebHookController extends Controller
{
    public function __construct(
        private InvoicePdfService    $pdfService,
        private BalesOtomatisService $waService
    ) {}

    /**
     * Handle notifikasi pembayaran dari Midtrans.
     *
     * Daftarkan URL ini di Midtrans Dashboard:
     * Settings → Configuration → Payment Notification URL
     * → https://yourdomain.com/webhook/midtrans
     *
     * PENTING: exclude dari CSRF di VerifyCsrfToken::$except
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('[Midtrans Webhook] Notifikasi masuk', [
            'order_id' => $payload['order_id'] ?? '-',
            'status'   => $payload['transaction_status'] ?? '-',
        ]);

        // 1. Verifikasi signature Midtrans
        if (!$this->verifySignature($payload)) {
            Log::warning('[Midtrans Webhook] Signature tidak valid');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId           = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'order_id tidak ada'], 400);
        }

        // 2. Cari transaksi
        $transaction = Transaction::with(['invoice'])
            ->where('order_id', $orderId)
            ->first();

        if (!$transaction) {
            Log::warning("[Midtrans Webhook] Transaksi tidak ditemukan: {$orderId}");
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // 3. Proses berdasarkan status
        if ($this->isSettlement($transactionStatus, $fraudStatus)) {
            $this->handleSettlement($transaction, $payload);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $this->handleCancel($transaction, $transactionStatus);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Handle transaksi yang berhasil dibayar (settlement).
     */
    private function handleSettlement(Transaction $transaction, array $payload): void
    {
        // Guard: skip jika sudah settlement sebelumnya
        if ($transaction->status === 'settlement') {
            Log::info("[Midtrans] {$transaction->order_id} sudah settlement, skip duplikat.");
            return;
        }

        DB::transaction(function () use ($transaction, $payload) {

            // ── Update status transaksi ──
            $transaction->update([
                'status'           => 'settlement',
                'payment_type'     => $payload['payment_type'] ?? $transaction->payment_type,
                'transaction_time' => $payload['settlement_time'] ?? now(),
            ]);

            // ── Buat atau update Invoice record ──
            $invoice = $this->ensureInvoiceExists($transaction);

            // ── Update status invoice ke settlement ──
            $invoice->update(['status' => 'settlement']);

            Log::info("[Invoice] Record invoice siap", [
                'invoice_number' => $invoice->invoice_number,
            ]);

            // ── Generate PDF & simpan ke storage ──
            try {
                // Refresh relasi invoice setelah update
                $transaction->setRelation('invoice', $invoice);

                $pdfUrl = $this->pdfService->generateAndStore($transaction);

                Log::info("[Invoice] PDF dibuat", ['url' => $pdfUrl]);

                // ── Kirim WA ke customer ──
                $sent = $this->waService->sendInvoice($transaction, $invoice, $pdfUrl);

                Log::info("[WA] Status pengiriman", [
                    'order_id' => $transaction->order_id,
                    'sent'     => $sent ? 'Berhasil' : 'Gagal',
                ]);

            } catch (\Exception $e) {
                // Jangan throw exception agar status transaksi tetap tersimpan
                Log::error('[Invoice/WA] Error saat generate/kirim: ' . $e->getMessage(), [
                    'order_id' => $transaction->order_id,
                    'trace'    => $e->getTraceAsString(),
                ]);
            }
        });
    }

    /**
     * Pastikan Invoice record ada untuk transaksi ini.
     * Jika belum ada, buat baru menggunakan Invoice::generateNumber().
     */
    private function ensureInvoiceExists(Transaction $transaction): Invoice
    {
        // Cek apakah invoice sudah ada
        if ($transaction->invoice) {
            return $transaction->invoice;
        }

        // Buat invoice baru dengan format nomor yang sudah ada di model
        $invoiceNumber = Invoice::generateNumber($transaction);

        return Invoice::create([
            'invoice_number' => $invoiceNumber,
            'transaction_id' => $transaction->id,
            'status'         => 'settlement',
            'created_by'     => null, // null = dibuat otomatis oleh sistem
        ]);
    }

    /**
     * Handle transaksi yang dibatalkan / kadaluarsa.
     */
    private function handleCancel(Transaction $transaction, string $status): void
    {
        $transaction->update(['status' => $status]);

        // Update status invoice juga jika ada
        if ($transaction->invoice) {
            $transaction->invoice->update(['status' => $status]);
        }

        Log::info("[Midtrans] Transaksi {$transaction->order_id} status: {$status}");
    }

    /**
     * Verifikasi signature dari Midtrans.
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     */
    private function verifySignature(array $payload): bool
    {
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey   = config('services.midtrans.server_key');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }

    /**
     * Cek apakah transaksi dianggap berhasil/lunas.
     */
    private function isSettlement(string $status, ?string $fraudStatus): bool
    {
        if ($status === 'capture') {
            return $fraudStatus === 'accept';
        }
        return $status === 'settlement';
    }
}