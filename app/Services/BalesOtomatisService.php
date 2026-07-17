<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BalesOtomatisService
{
    private string $apiKey;
    private string $numberId;
    private string $baseUrl = 'https://api.balesotomatis.id/public/v1';

    public function __construct()
    {
        $this->apiKey   = config('services.balesotomatis.api_key');
        $this->numberId = config('services.balesotomatis.number_id');
    }

    /**
     * Kirim notifikasi invoice ke WhatsApp customer.
     */
    public function sendInvoice(Transaction $transaction, Invoice $invoice, string $pdfUrl): bool
    {
        $phone   = $this->formatPhone($transaction->phone);
        $message = $this->buildMessage($transaction, $invoice, $pdfUrl);

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/send_personal_file", [
                    'api_key'       => $this->apiKey,
                    'number_id'     => $this->numberId,
                    'phone_no'      => $phone,
                    'country_code'  => '62',
                    'message'       => $message,
                    'file_url'      => $pdfUrl,
                    'enable_typing' => '1',
                    'method_send'   => 'async',
                ]);

            $body = $response->json();
            $responseCode = $body['code'] ?? null;

            // BalesOtomatis selalu return HTTP 200, cek isi body-nya
            $isSuccess = $response->successful()
                && $responseCode !== '404'
                && $responseCode !== '401'
                && $responseCode !== '400'
                && !isset($body['type']);

            if ($isSuccess) {
                Log::info('[BalesOtomatis] Invoice WA terkirim', [
                    'order_id'       => $transaction->order_id,
                    'invoice_number' => $invoice->invoice_number,
                    'phone'          => $phone,
                    'response'       => $body,
                ]);
                return true;
            }

            Log::error('[BalesOtomatis] Gagal kirim WA', [
                'order_id'     => $transaction->order_id,
                'http_status'  => $response->status(),
                'response_code'=> $responseCode,
                'message'      => $body['message'] ?? '-',
                'body'         => $body,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('[BalesOtomatis] Exception: ' . $e->getMessage(), [
                'order_id' => $transaction->order_id,
            ]);
            return false;
        }
    }

    /**
     * Bangun teks pesan WA dengan format yang rapi.
     */
    private function buildMessage(Transaction $transaction, Invoice $invoice, string $pdfUrl): string
    {
        $bookingDate  = $transaction->booking_date
            ? $transaction->booking_date->format('d F Y')
            : '-';

        $totalAmount  = 'Rp ' . number_format($transaction->total_amount, 0, ',', '.');
        $duration     = $transaction->duration_text ?? '-';
        $participants = $transaction->participants_text ?? '-';

        return "Halo *{$transaction->nama_lengkap}* 👋\n\n"
            . "Terima kasih telah memesan layanan *Urban Office*!\n"
            . "Pembayaran Anda telah berhasil dikonfirmasi ✅\n\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "🧾 *DETAIL BOOKING*\n"
            . "━━━━━━━━━━━━━━━━━━━━\n"
            . "📋 No. Invoice : *{$invoice->invoice_number}*\n"
            . "🏢 Layanan     : {$transaction->room_type}\n"
            . "📅 Tanggal     : {$bookingDate}\n"
            . "⏱️ Durasi      : {$duration}\n"
            . "👥 Peserta     : {$participants}\n"
            . "💰 Total Bayar : *{$totalAmount}*\n"
            . "━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📄 *Download Invoice PDF Anda:*\n"
            . "👉 {$pdfUrl}\n\n"
            . "_Invoice berlaku sebagai bukti pembayaran resmi._\n\n"
            . "Hubungi kami jika ada pertanyaan 😊\n"
            . "*Urban Office Team*";
    }

    /**
     * Format nomor HP ke format internasional 628xxx.
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}