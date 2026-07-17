<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Midtrans\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\LunchOption;
use Carbon\Carbon;
use App\Traits\DocumentHelperTrait;
use App\Services\PromoService;
use App\Models\Promo;


class TransactionController extends Controller
{
    use DocumentHelperTrait;

    public function __construct()
    {
        try {
            // Load konfigurasi Midtrans dari config/midtrans.php
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('...is_production');
            Config::$isSanitized  = true;
            Config::$is3ds        = true;

            // Validasi konfigurasi Midtrans
            if (empty(Config::$serverKey)) {
                throw new Exception('Midtrans server key not configured');
            }
        } catch (Exception $e) {
            Log::error('Midtrans configuration error: ' . $e->getMessage());
        }
    }
    
// function kirim data baru ke tabel transaction 
public function store(Request $request)
{
    DB::beginTransaction();

    try {
        Log::info('Booking request received', $request->all());

        // ✅ VALIDASI INPUT
        $validated = $request->validate([
            'city_id'       => 'required|exists:citys,id',
            'location_id'   => 'required|exists:locations,id',
            'room_id'       => 'nullable|exists:rooms,id',
            'room_type'     => 'required|string|in:Virtual Office,Event Space,Meeting Room,Coworking Space,Private Office,Sharing Room',

            'nama_lengkap'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'nullable|string|max:20',

            'booking_date'  => 'nullable|date|after_or_equal:today',
            'contract_date' => 'nullable|date|after_or_equal:today',
            'start_time'    => 'nullable|date_format:H:i',
            'jumlah_orang'  => 'nullable|integer|min:1|max:1000',

            'paket'   => 'required|string|in:hourly,daily,weekly,monthly,yearly',
            'jam'     => 'nullable|integer|min:1|max:24',
            'hari'    => 'nullable|integer|min:1|max:31',
            'minggu'  => 'nullable|integer|min:1|max:52',
            'bulan'   => 'nullable|integer|min:1|max:12',
            'tahun'   => 'nullable|integer|min:1|max:5',

            'service_category_id' => 'nullable|exists:service_categories,id',
            'status_pkp'    => 'nullable|in:Non PKP,PKP',
            'coffee_break'  => 'nullable|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'company_address' => ['nullable','string','max:1000',\Illuminate\Validation\Rule::requiredIf(in_array($request->room_type, ['Virtual Office', 'Private Office'])),],
            'notes'         => 'nullable|string',
            'nik'           => 'nullable|string|max:255',
            'npwp'          => 'nullable|string|max:50',

            'subtotal'      => 'required|numeric|min:0',
            'admin_fee'     => 'required|numeric|min:0',
            'deposit'       => 'required|numeric|min:0',
            'total_amount'  => 'required|numeric|min:0',

            'lunches' => 'nullable|array',
            'lunches.*.lunch_option_id' => 'required_with:lunches|exists:lunch_options,id',
            'lunches.*.quantity' => 'required_with:lunches|integer|min:1',
            'lunch_total' => 'nullable|numeric|min:0',
            
            'promo_code'    => 'nullable|string',
            'discount_amount'=> 'nullable|numeric|min:0',
        ]);

        $userId = Auth::id();
        $amount = (int) round($validated['total_amount']);
        $orderId = 'ORDER-' . strtoupper(Str::random(10)) . '-' . time();

        // ✅ NORMALISASI DURASI
        $duration = [
            'jam'    => null, 'hari' => null, 'minggu' => null, 'bulan' => null, 'tahun' => null,
        ];

        switch ($validated['paket']) {
            case 'hourly':  $duration['jam']    = $validated['jam'] ?? null; break;
            case 'daily':   $duration['hari']   = $validated['hari'] ?? null; break;
            case 'weekly':  $duration['minggu'] = $validated['minggu'] ?? null; break;
            case 'monthly': $duration['bulan']  = $validated['bulan'] ?? null; break;
            case 'yearly':  $duration['tahun']  = $validated['tahun'] ?? null; break;
        }

        $durationValue = $duration['jam'] ?? $duration['hari'] ?? $duration['minggu'] ?? $duration['bulan'] ?? $duration['tahun'];
        if (empty($durationValue)) {
            return response()->json(['success' => false, 'message' => 'Durasi pemesanan tidak valid'], 422);
        }

        $room = !empty($validated['room_id']) ? Room::find($validated['room_id']) : null;
        if ($room && !empty($room->room_number)) {
            $room_name = $validated['room_type'] . ' - Ruang ' . $room->room_number;
        } else {
            $room_name = $validated['room_type']; // ✅ Langsung pakai room_type tanpa prefix "Meeting Room"
        }

        // ✅ SIMPAN TRANSAKSI AWAL
        $transaction = Transaction::create([
            'user_id'      => $userId,
            'city_id'      => $validated['city_id'],
            'location_id'  => $validated['location_id'],
            'room_id'      => $validated['room_id'] ?? null,
            'room_type'    => $validated['room_type'],
            'jumlah_orang' => $validated['jumlah_orang'] ?? null,
            'booking_date' => $validated['booking_date'] ?? null,
            'contract_date'=> $validated['contract_date'] ?? null,
            'start_time'   => $validated['start_time'] ?? null,

            'paket'        => $validated['paket'],
            'jam'          => $duration['jam'],
            'hari'         => $duration['hari'],
            'minggu'       => $duration['minggu'],
            'bulan'        => $duration['bulan'],
            'tahun'        => $duration['tahun'],

            'service_category_id' => $validated['service_category_id'] ?? null,
            'status_pkp'   => $validated['status_pkp'] ?? 'Non PKP',
            'coffee_break' => $validated['coffee_break'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'company_address' => $validated['company_address'] ?? null,
            'notes'        => $validated['notes'] ?? null,
            'nik'          => $validated['nik'] ?? null,
            'npwp'         => $validated['npwp'] ?? null,

            'phone'        => $validated['phone'] ?? null,
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],

            'deposit'      => (int) round($validated['deposit']),
            'gross_amount' => $amount,
            'lunch_total'  => 0,

            'order_id'     => $orderId,
            'payment_type' => null,
            'status'       => 'pending',
            'snap_token'   => null,
            'transaction_time' => now(),
            'is_read'      => false,
            
            'promo_code'   => $validated['promo_code'] ?? null,
            'discount_amount' => 0, // Akan diupdate nanti setelah divalidasi
        ]);

        // ✅ PROCESS LUNCH ITEMS - GUNAKAN HARGA DARI DATABASE
        $calculatedLunchTotal = 0;
        if (!empty($validated['lunches']) && is_array($validated['lunches'])) {
            Log::info('Processing lunch items', ['count' => count($validated['lunches'])]);

            foreach ($validated['lunches'] as $index => $lunchData) {
                $lunchOption = LunchOption::find($lunchData['lunch_option_id']);
                if (!$lunchOption) {
                    throw new \Exception("Lunch option ID {$lunchData['lunch_option_id']} tidak ditemukan");
                }
                if (!$lunchOption->is_available) {
                    throw new \Exception("Lunch option '{$lunchOption->name}' sedang tidak tersedia");
                }

                // ✅ GUNAKAN HARGA DARI DATABASE, BUKAN DARI CLIENT
                $qty = (int) ($lunchData['quantity'] ?? 1);
                $unitPrice = (int) round((float) $lunchOption->price); // HARGA DARI DB

                $lunchItem = $transaction->lunches()->create([
                    'lunch_option_id' => $lunchData['lunch_option_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                ]);

                $lineSubtotal = $qty * $unitPrice;
                $calculatedLunchTotal += $lineSubtotal;

                Log::info("Lunch item saved", [
                    'id' => $lunchItem->id,
                    'lunch_option' => $lunchOption->name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);
            }

            $transaction->update(['lunch_total' => $calculatedLunchTotal]);
            Log::info('Lunch processing completed', [
                'transaction_id' => $transaction->id,
                'items_count' => $transaction->lunches()->count(),
                'lunch_total' => $calculatedLunchTotal
            ]);
        }

        // ✅ HITUNG ULANG HARGA DENGAN LUNCH TOTAL YANG BARU
        $lunchTotal = (int) $transaction->lunch_total;
        $subtotalFromClient = (int) round($validated['subtotal']);
        $deposit = (int) round($validated['deposit']);
        $adminFee = (int) round($validated['admin_fee']);

        // ✅ VALIDASI: JIKA LUNCH TOTAL BERBEDA DENGAN CLIENT, GUNAKAN YANG SERVER-SIDE
        $serverSideSubtotal = $subtotalFromClient;
        if ($calculatedLunchTotal != $validated['lunch_total']) {
            Log::warning('Lunch total mismatch, using server-side calculation', [
                'client_lunch_total' => $validated['lunch_total'],
                'server_lunch_total' => $calculatedLunchTotal
            ]);
            // Sesuaikan room price berdasarkan lunch total yang benar
            $serverSideSubtotal = $subtotalFromClient - $validated['lunch_total'] + $calculatedLunchTotal;
        }

        $roomPrice = $serverSideSubtotal - $lunchTotal;

        if ($roomPrice < 0) {
            throw new \Exception("Perhitungan room price menghasilkan nilai negatif.");
        }

        // ✅ PROMO CALCULATION SERVER-SIDE — via PromoService
        $promoCode            = $validated['promo_code'] ?? null;
        $serverDiscountAmount = 0;
        $validatedPromo       = null;

        if ($promoCode) {
            /** @var PromoService $promoService */
            $promoService  = app(PromoService::class);
            $locationIdStr = (string) $validated['location_id'];
            $roomTypeName  = $validated['room_type'];

            $promoResult = $promoService->validate(
                $promoCode,
                $userId,
                $serverSideSubtotal,
                $locationIdStr,
                $roomTypeName
            );

            if ($promoResult['valid']) {
                $serverDiscountAmount = (int) round($promoResult['discount_amount']);
                $validatedPromo       = $promoResult['promo'];

                Log::info('Promo validated via PromoService', [
                    'promo_code'      => $promoCode,
                    'discount_amount' => $serverDiscountAmount,
                    'promo_id'        => $validatedPromo->id,
                ]);
            } else {
                Log::warning('Promo validation failed in store()', [
                    'promo_code' => $promoCode,
                    'reason'     => $promoResult['message'],
                ]);
                // Lanjutkan transaksi tanpa diskon — jangan batalkan booking
                $serverDiscountAmount = 0;
            }
        }

        
        // Update model with the real calculated discount
        if ($serverDiscountAmount > 0) {
             $transaction->updateQuietly(['discount_amount' => $serverDiscountAmount]);
        }

        $netSubtotal = max(0, $serverSideSubtotal - $serverDiscountAmount);
        $serverAdminFee = (int) round($netSubtotal * 0.10);
        $serverDiscountAdminFee = $serverAdminFee; // Sesuai dengan frontend: auto-potong admin fee

        // ✅ VALIDASI FINAL AMOUNT
        $expectedTotal = $netSubtotal + $serverAdminFee - $serverDiscountAdminFee + $deposit;
        if ($expectedTotal !== $amount) {
            Log::warning('Amount mismatch, adjusting gross_amount', [
                'expected' => $expectedTotal,
                'actual' => $amount
            ]);
            // Update amount dengan perhitungan server-side
            $amount = $expectedTotal;
        }

        // ✅ KONFIGURASI MIDTRANS - PASTIKAN INI DIPANGGIL
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        \Midtrans\Config::$overrideNotifUrl = 'https://0dad-114-5-111-86.ngrok-free.app/midtrans/notification';

        // ✅ TAMBAHKAN LOG INI UNTUK DEBUG
        Log::info('🔧 NOTIFICATION URL CONFIG', [
            'override_url' => \Midtrans\Config::$overrideNotifUrl,
            'using_ngrok' => true
        ]);

        Log::info('Midtrans Config', [
            'is_production' => config('midtrans.is_production'),
            'server_key_prefix' => substr(config('midtrans.server_key'), 0, 10),
            'merchant_id' => config('midtrans.merchant_id')
        ]);

        // ✅ BUILD MIDTRANS PARAMS - DENGAN DETAIL LENGKAP UNTUK INVOICE
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [],
        ];

        // ✅ ITEM 1: ROOM BOOKING DENGAN DETAIL LENGKAP
        if ($roomPrice > 0) {
            $params['item_details'][] = [
                'id'       => 'room-' . ($transaction->room_id ?? 'virtual'),
                'price'    => (int) $roomPrice,
                'quantity' => 1,
                'name'     => substr($room_name, 0, 50),
            ];
        }

        // ✅ ITEM 2: PAKET DURASI (PRICE 0 UNTUK INFO)
        $paketLabel = ucfirst($validated['paket']);
        $durasiText = '';
        switch ($validated['paket']) {
            case 'hourly':
                $durasiText = $duration['jam'] . ' Jam';
                break;
            case 'daily':
                $durasiText = $duration['hari'] . ' Hari';
                break;
            case 'weekly':
                $durasiText = $duration['minggu'] . ' Minggu';
                break;
            case 'monthly':
                $durasiText = $duration['bulan'] . ' Bulan';
                break;
            case 'yearly':
                $durasiText = $duration['tahun'] . ' Tahun';
                break;
        }

        $params['item_details'][] = [
            'id'       => 'package-info',
            'price'    => 0,
            'quantity' => 1,
            'name'     => "Paket {$paketLabel} ({$durasiText})",
        ];

        // ✅ ITEM 3: HEADER INFO BOOKING
        $params['item_details'][] = [
            'id'       => 'info-booking-header',
            'price'    => 0,
            'quantity' => 1,
            'name'     => '== Info Booking ==',
        ];

        // ✅ ITEM 4: TANGGAL BOOKING
        if (!empty($validated['booking_date'])) {
            $formattedDate = \Carbon\Carbon::parse($validated['booking_date'])->format('d F Y');
            $params['item_details'][] = [
                'id'       => 'booking-date',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Tanggal: {$formattedDate}",
            ];
        }

        // ✅ ITEM 5: JAM MULAI
        if (!empty($validated['start_time'])) {
            $params['item_details'][] = [
                'id'       => 'start-time',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Jam Mulai: {$validated['start_time']}",
            ];
        }

        // ✅ ITEM 6: JUMLAH ORANG
        if (!empty($validated['jumlah_orang'])) {
            $params['item_details'][] = [
                'id'       => 'guest-count',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Jumlah Orang: {$validated['jumlah_orang']} orang",
            ];
        }

        // ✅ ITEM 7: HEADER INFO PEMESAN
        $params['item_details'][] = [
            'id'       => 'info-customer-header',
            'price'    => 0,
            'quantity' => 1,
            'name'     => '== Info Pemesan ==',
        ];

        // ✅ ITEM 8: NAMA PEMESAN
        $params['item_details'][] = [
            'id'       => 'customer-name',
            'price'    => 0,
            'quantity' => 1,
            'name'     => "Nama: {$validated['nama_lengkap']}",
        ];

        // ✅ ITEM 9: EMAIL PEMESAN
        $params['item_details'][] = [
            'id'       => 'customer-email',
            'price'    => 0,
            'quantity' => 1,
            'name'     => "Email: {$validated['email']}",
        ];

        // ✅ ITEM 10: NO HP PEMESAN
        if (!empty($validated['phone'])) {
            $params['item_details'][] = [
                'id'       => 'customer-phone',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "No HP: {$validated['phone']}",
            ];
        }

        // ✅ ITEM 10.1: PERUSAHAAN (JIKA ADA)
        if (!empty($transaction->company_name)) {
            $params['item_details'][] = [
                'id'       => 'customer-company',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Perusahaan: {$transaction->company_name}",
            ];
        }

        // ✅ ITEM 10.2: NIK (JIKA ADA)
        if (!empty($transaction->nik)) {
            $params['item_details'][] = [
                'id'       => 'customer-nik',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "NIK: {$transaction->nik}",
            ];
        }

        // ✅ ITEM 10.3: COFFEE BREAK (JIKA ADA)
        if (!empty($transaction->coffee_break)) {
            $params['item_details'][] = [
                'id'       => 'customer-coffee-break',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Coffee Break: {$transaction->coffee_break}",
            ];
        }

        // ✅ ITEM 10.4: CATATAN (JIKA ADA)
        if (!empty($transaction->notes)) {
            $notesPreview = mb_substr($transaction->notes, 0, 30) . (mb_strlen($transaction->notes) > 30 ? '...' : '');
            $params['item_details'][] = [
                'id'       => 'customer-notes',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "Catatan: {$notesPreview}",
            ];
        }

        // ✅ ITEM 10.2.1: NPWP (JIKA ADA) - TAMBAHKAN INI
        if (!empty($transaction->npwp)) {
            // Format NPWP agar lebih rapi (opsional)
            $npwpFormatted = $transaction->npwp;
            // Jika NPWP 15 digit, format jadi 99.999.999.9-999.999
            if (preg_match('/^\d{15}$/', $transaction->npwp)) {
                $npwpFormatted = substr($transaction->npwp, 0, 2) . '.' .
                                substr($transaction->npwp, 2, 3) . '.' .
                                substr($transaction->npwp, 5, 3) . '.' .
                                substr($transaction->npwp, 8, 1) . '-' .
                                substr($transaction->npwp, 9, 3) . '.' .
                                substr($transaction->npwp, 12, 3);
            }
            
            $params['item_details'][] = [
                'id'       => 'customer-npwp',
                'price'    => 0,
                'quantity' => 1,
                'name'     => "NPWP: {$npwpFormatted}",
            ];
        }

        // ✅ ITEM 11: LUNCH ITEMS (JIKA ADA)
        if ($transaction->lunches()->count() > 0) {
            foreach ($transaction->lunches as $index => $lunchItem) {
                $lunchName = $lunchItem->lunchOption->name ?? 'Lunch Item';
                $params['item_details'][] = [
                    'id'       => 'lunch-' . $lunchItem->lunch_option_id,
                    'price'    => (int) $lunchItem->unit_price,
                    'quantity' => (int) $lunchItem->quantity,
                    'name'     => $lunchName,
                ];
            }
        }

        // ✅ ITEM 12: DEPOSIT (JIKA ADA)
        if ($deposit > 0) {
            $params['item_details'][] = [
                'id'       => 'deposit',
                'price'    => (int) $deposit,
                'quantity' => 1,
                'name'     => 'Deposit',
            ];
        }

        // ✅ ITEM 13: DISKON PROMO (JIKA ADA)
        if ($serverDiscountAmount > 0) {
            $params['item_details'][] = [
                'id'       => 'promo-' . $promoCode,
                'price'    => -((int) $serverDiscountAmount),
                'quantity' => 1,
                'name'     => 'Diskon Promo ' . $promoCode,
            ];
        }
        
        // ✅ ITEM 14: ADMIN FEE
        if ($serverAdminFee > 0) {
            $params['item_details'][] = [
                'id'       => 'admin-fee',
                'price'    => (int) $serverAdminFee,
                'quantity' => 1,
                'name'     => 'Admin Fee',
            ];
        }

        // ✅ ITEM 15: DISKON ADMIN FEE (JIKA ADA)
        if ($serverDiscountAdminFee > 0) {
            $params['item_details'][] = [
                'id'       => 'admin-fee-discount',
                'price'    => -((int) $serverDiscountAdminFee),
                'quantity' => 1,
                'name'     => 'Promo Admin Fee Gratis',
            ];
        }
        
        // ✅ CUSTOMER DETAILS
        $params['customer_details'] = [
            'first_name' => substr($transaction->nama_lengkap, 0, 50),
            'email'      => $transaction->email,
            'phone'      => $transaction->phone ?? '',
        ];

        // ✅ CALLBACKS
        $params['callbacks'] = [
            'finish'  => route('payment.finish') . '?order_id=' . $orderId,
            'error'   => route('payment.error') . '?order_id=' . $orderId,
            'pending' => route('payment.unfinish') . '?order_id=' . $orderId,
        ];

        // ✅ VALIDASI FINAL ITEMS TOTAL
        $itemsTotal = 0;
        foreach ($params['item_details'] as $item) {
            $itemsTotal += ((int)$item['price'] * (int)$item['quantity']);
        }

        // ✅ LOG DETAILED PARAMETERS UNTUK DEBUG
        Log::info('=== DETAILED MIDTRANS PARAMETERS ===', [
            'order_id' => $orderId,
            'gross_amount' => $amount,
            'calculated_items_total' => $itemsTotal,
            'item_count' => count($params['item_details']),
            'environment' => config('midtrans.is_production') ? 'production' : 'sandbox'
        ]);

        // Log setiap item secara terpisah
        foreach ($params['item_details'] as $index => $item) {
            Log::info("Item {$index}", [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'line_total' => $item['price'] * $item['quantity']
            ]);
        }

        if ($itemsTotal !== $amount) {
            throw new \Exception("Total item_details ({$itemsTotal}) tidak sama dengan gross_amount ({$amount}).");
        }

        // ✅ CREATE MIDTRANS TRANSACTION
        try {
            Log::info('Creating Midtrans transaction...', ['order_id' => $orderId]);
            $midtrans = \Midtrans\Snap::createTransaction($params);
            Log::info('Midtrans transaction created successfully', [
                'token' => $midtrans->token,
                'redirect_url' => $midtrans->redirect_url
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans API Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'order_id' => $orderId
            ]);
            throw new \Exception("Gagal membuat transaksi Midtrans: " . $e->getMessage());
        }

        // ✅ UPDATE TRANSAKSI DENGAN DATA MIDTRANS
        $transaction->update([
            'snap_token'   => $midtrans->token,
            'redirect_url' => $midtrans->redirect_url,
            'gross_amount' => $amount,
        ]);

        Log::info("=== TRANSACTION FINALIZED ===", [
            'order_id' => $orderId,
            'token' => $midtrans->token,
            'amount' => $amount,
        ]);

        DB::commit();

        return response()->json([
            'success'      => true,
            'message'      => 'Transaksi berhasil dibuat',
            'snap_token'   => $midtrans->token,
            'redirect_url' => $midtrans->redirect_url,
            'order_id'     => $orderId,
            'transaction'  => $transaction->load('lunches.lunchOption'),
        ]);

    } catch (ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error', ['errors' => $e->errors()]);
        return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Transaction creation failed', [
            'error'   => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}


/**
 * ✅ Generate label durasi untuk midtrans
 */
private function generateDurationLabel($paket, $duration)
{
    switch ($paket) {
        case 'hourly':  return ($duration['jam'] ?? 0)    . ' Jam';
        case 'daily':   return ($duration['hari'] ?? 0)   . ' Hari';
        case 'weekly':  return ($duration['minggu'] ?? 0) . ' Minggu';
        case 'monthly': return ($duration['bulan'] ?? 0)  . ' Bulan';
        case 'yearly':  return ($duration['tahun'] ?? 0)  . ' Tahun';
        default: return '';
    }
}


public function notificationHandler(Request $request)
{
    // ✅ LOG RAW REQUEST
    Log::info('=== MIDTRANS NOTIFICATION RAW ===', [
        'content' => $request->getContent()
    ]);

    // ✅ PASTIKAN HTTP 200
    http_response_code(200);
    header('Content-Type: application/json');

    try {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Ambil notifikasi
        $notif = new \Midtrans\Notification();
        
        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $paymentType = $notif->payment_type;
        $fraudStatus = $notif->fraud_status ?? null;

        Log::info('Processing notification', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'payment_type' => $paymentType
        ]);

        if (!$orderId) {
            echo json_encode(['status' => 'OK']);
            exit;
        }

        // ✅ CARI TRANSAKSI
        $transaction = Transaction::where('order_id', $orderId)->first();
        
        if (!$transaction) {
            Log::error('Transaction not found: ' . $orderId);
            echo json_encode(['status' => 'OK']);
            exit;
        }

        // ✅ UPDATE STATUS - PASTIKAN INI
        $oldStatus = $transaction->status;
        
        // Mapping status
        switch ($transactionStatus) {
            case 'capture':
                $transaction->status = ($fraudStatus == 'challenge') ? 'challenge' : 'settlement';
                break;
                
            case 'settlement':
                $transaction->status = 'settlement';
                break;
                
            case 'pending':
                $transaction->status = 'pending';
                break;
                
            case 'deny':
            case 'cancel':
            case 'expire':
                $transaction->status = 'failed';
                break;
                
            default:
                $transaction->status = 'pending';
        }

        // ✅ UPDATE PAYMENT TYPE
        $transaction->payment_type = $paymentType;
        
        // ✅ GUNAKAN DB TRANSACTION UNTUK UPDATE
        DB::beginTransaction();
        try {
            $transaction->save();

            // ← TAMBAHAN: trigger generate contract dan addendum jika settlement
            if ($transaction->status === 'settlement') {
                // ── Catat pemakaian promo via PromoService (dengan lockForUpdate & idempotency) ──
                if ($transaction->promo_code) {
                    $promo = Promo::where('code', $transaction->promo_code)->first();
                    if ($promo) {
                        try {
                            app(PromoService::class)->recordUsage(
                                $promo,
                                (int) $transaction->user_id,
                                (int) $transaction->id,
                                (string) $transaction->location_id,
                                (float) $transaction->discount_amount,
                                (float) $transaction->gross_amount,
                                [
                                    'order_id'    => $transaction->order_id,
                                    'room_type'   => $transaction->room_type,
                                    'payment_type'=> $transaction->payment_type,
                                ]
                            );
                        } catch (\Exception $promoEx) {
                            // Jangan batalkan transaksi karena masalah pencatatan promo
                            Log::error('[notificationHandler] Gagal recordUsage promo', [
                                'transaction_id' => $transaction->id,
                                'promo_code'     => $transaction->promo_code,
                                'error'          => $promoEx->getMessage(),
                            ]);
                        }
                    }
                }
                
                if (in_array($transaction->room_type, ['Virtual Office', 'Private Office'])) {
                    $this->handleSettlementForInitialContract($transaction);
                    $this->handleSettlementForAddendum($transaction);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save transaction', [
                'error' => $e->getMessage()
            ]);
        }

        echo json_encode(['success' => true]);
        exit;

    } catch (\Exception $e) {
        Log::error('Midtrans notification error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        echo json_encode(['status' => 'OK']);
        exit;
    }
}

/**
 * Handle generate initial contract setelah payment settlement
 */
private function handleSettlementForInitialContract(Transaction $transaction): void
{
    try {
        if (!in_array($transaction->room_type, ['Virtual Office', 'Private Office'])) {
            return;
        }

        // Cek apakah transaksi ini adalah perpanjangan (Addendum)
        // Jika ya, skip generasi initial contract karena akan dihandle oleh handleSettlementForAddendum
        $isAddendum = \App\Models\Addendum::where('transaction_id', $transaction->id)->exists();
        if ($isAddendum) {
            return;
        }

        // Pastikan tidak ada contract active/renewed untuk transaksi ini yang menimpa
        $existingContract = \App\Models\Contract::where('transaction_id', $transaction->id)->first();
        if ($existingContract && in_array($existingContract->status, ['active', 'renewed', 'expired', 'terminated'])) {
            Log::info('Contract already fully generated for transaction', [
                'transaction_id' => $transaction->id
            ]);
            return;
        }

        // Kalau tidak ada contract date, fallback ke hari settlement
        $contractDateStr = $transaction->contract_date ? \Carbon\Carbon::parse($transaction->contract_date)->toDateString() : now()->toDateString();
        $contractDate = \Carbon\Carbon::parse($contractDateStr);

        $invoice = $transaction->invoice;
        if (!$invoice) {
            Log::warning('No invoice found for transaction during initial contract generation', ['transaction_id' => $transaction->id]);
        }

        // Buat atau cari draft kontrak
        $contract = \App\Models\Contract::firstOrCreate(
            ['transaction_id' => $transaction->id, 'status' => 'draft'],
            [
                'invoice_id'    => $invoice ? $invoice->id : null,
                'created_by'    => $transaction->user_id, // since there's no admin
                'type'          => $transaction->room_type,
                'contract_date' => $contractDate->toDateString(),
            ]
        );

        // =============================================
        // PERSIAPAN DATA UNTUK PDF
        // =============================================
        $startDate  = $contract->start_date ?? $contractDate->copy();
        $bulan      = $transaction->bulan ?? 12;
        $tahun      = $transaction->tahun ?? null;

        $endDate    = $tahun
            ? $startDate->copy()->addYears($tahun)
            : $startDate->copy()->addMonths($bulan);

        $durasiTeks = $tahun
            ? $tahun . ' (' . $this->numberToWords($tahun) . ') tahun'
            : $bulan . ' (' . $this->numberToWords($bulan) . ') bulan';

        // =============================================
        // KALKULASI NOMINAL SEWA (gross - deposit)
        // =============================================
        $deposit     = (int) ($transaction->deposit ?? 0);
        $grossAmount = (int) $transaction->gross_amount;
        $sewaAmount  = $grossAmount - $deposit;

        $contract->save();
        $sequence       = str_pad($contract->id, 3, '0', STR_PAD_LEFT);
        
        $typeCode = $transaction->room_type === 'Virtual Office' ? 'VO' : 'PO'; 
        $contractNumber = $transaction->order_id . '/' . $typeCode . '/' . $sequence . '/Urban Office/' . $this->romanize($contractDate->month) . '/' . $contractDate->year;

        // =============================================
        // GENERATE PUBLIC TOKEN & QR CODE
        // =============================================
        if (!$contract->hasPublicToken()) {
            $this->generatePublicToken($contract);
            $contract->refresh();
        }

        $qrCodeBase64 = $this->generateQrCodeBase64($contract->public_token);

        // =============================================
        // GENERATE PDF
        // =============================================
        
        // Asumsi kita menggunakan view yang sama atau serupa dengan VO
        $viewStr = 'layouts.admin.virtual-office-pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewStr, [
            'contract'          => $contract,
            'transaction'       => $transaction,
            'invoice'           => $invoice,
            'contractDate'      => $contractDate,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'durasiTeks'        => $durasiTeks,
            'contractNumber'    => $contractNumber,
            'terbilang_amount'  => $this->amountToWords($grossAmount),
            'terbilang_deposit' => $this->amountToWords($deposit),
            'sewa_amount'       => $sewaAmount,
            'terbilang_sewa'    => $this->amountToWords($sewaAmount),
            'qrCodeBase64'      => $qrCodeBase64,
        ])->setPaper('A4', 'portrait');

        // =============================================
        // SIMPAN FILE PDF KE STORAGE
        // =============================================
        $filename = 'Kontrak-' . $typeCode . '-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $transaction->order_id) . '-' . $contractDate->format('Ymd') . '.pdf';
        $filePath = 'contracts/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $pdf->output());

        // =============================================
        // UPDATE RECORD CONTRACT
        // =============================================
        $contract->update([
            'status'          => 'active',
            'contract_number' => $contractNumber,
            'contract_date'   => $contractDate->toDateString(),
            'start_date'      => $startDate->toDateString(),
            'end_date'        => $endDate->toDateString(),
            'file_path'       => $filePath,
            'created_by'      => $transaction->user_id, // No admin involvement
        ]);

        Log::info('Initial Contract PDF generated automatically on settlement.', [
            'transaction_id'  => $transaction->id,
            'contract_id'     => $contract->id,
            'contract_number' => $contractNumber,
            'file_path'       => $filePath,
        ]);

    } catch (\Exception $e) {
        Log::error('handleSettlementForInitialContract failed', [
            'transaction_id' => $transaction->id,
            'error'          => $e->getMessage(),
            'trace'          => $e->getTraceAsString(),
        ]);
    }
}

/**
 * Handle generate addendum/contract setelah payment settlement
 */
private function handleSettlementForAddendum(Transaction $transaction): void
{
    try {
        // Hanya proses untuk Virtual Office
        if ($transaction->room_type !== 'Virtual Office') {
            return;
        }

        // Cek apakah ada draft addendum untuk transaksi ini
        $draftAddendum = \App\Models\Addendum::where('transaction_id', $transaction->id)
                                             ->where('status', 'draft')
                                             ->with([
                                                 'contract.transaction',
                                                 'transaction.location.city',
                                                 'parentAddendum',
                                                 'invoice',
                                             ])
                                             ->first();

        if (!$draftAddendum) {
            Log::info('No draft addendum found for transaction', [
                'transaction_id' => $transaction->id
            ]);
            return;
        }

        // Generate addendum number
        // Format: order_id/sequence/Urban Office/bulan_romawi/tahun
        $addendumDate   = \Carbon\Carbon::parse($draftAddendum->addendum_date);
        $romanMonth     = $this->romanize($addendumDate->month);
        $addendumNumber = $transaction->order_id . '/'
                        . $draftAddendum->sequence_number . '/Urban Office/'
                        . $romanMonth . '/'
                        . $addendumDate->year;

        // =============================================
        // GENERATE PDF ADDENDUM
        // =============================================
        $originalContract = $draftAddendum->contract;
        $parentAddendum   = $draftAddendum->parentAddendum;

        // =============================================
        // GENERATE PUBLIC TOKEN & QR CODE
        // =============================================
        if (!$draftAddendum->public_token) {
            $this->generatePublicToken($draftAddendum);
            $draftAddendum->refresh();
        }

        $qrCodeBase64 = $this->generateQrCodeBase64($draftAddendum->public_token, 'addendum.public.verify');

        // Set properti memory agr terbaca di Blade (PDF Generator)
        $draftAddendum->addendum_number = $addendumNumber;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('layouts.admin.virtual-office-addendum-pdf', [
            'addendum'           => $draftAddendum,
            'transaction'        => $transaction,
            'originalContract'   => $originalContract,
            'parentAddendum'     => $parentAddendum,
            'terbilang_baru'     => $this->amountToWords((int) $draftAddendum->gross_amount),
            'terbilang_original' => $this->amountToWords((int) ($originalContract->transaction->gross_amount ?? 0)),
            'terbilang_sebelum'  => $parentAddendum
                                    ? $this->amountToWords((int) $parentAddendum->gross_amount)
                                    : '',
            'qrCodeBase64'       => $qrCodeBase64,
        ])->setPaper('A4', 'portrait');

        // Simpan PDF ke storage
        $filename = 'Addendum-' . $draftAddendum->roman_order
                  . '-' . preg_replace('/[^A-Za-z0-9\-]/', '-', $transaction->order_id)
                  . '-' . $addendumDate->format('Ymd')
                  . '.pdf';

        $filePath = 'addendums/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $pdf->output());

        Log::info('Addendum PDF generated', [
            'addendum_id' => $draftAddendum->id,
            'file_path'   => $filePath,
        ]);

        // =============================================
        // UPDATE ADDENDUM → ACTIVE
        // =============================================
        $draftAddendum->update([
            'addendum_number' => $addendumNumber,
            'file_path'       => $filePath,
            'status'          => 'active',
        ]);

        // Update invoice → settlement
        if ($draftAddendum->invoice) {
            $draftAddendum->invoice->update(['status' => 'settlement']);
        }

        // Update original contract → renewed
        if ($originalContract && $originalContract->status !== 'renewed') {
            $originalContract->update(['status' => 'renewed']);
        }

        Log::info('Addendum activated after settlement', [
            'transaction_id'  => $transaction->id,
            'addendum_id'     => $draftAddendum->id,
            'addendum_number' => $addendumNumber,
            'addendum_order'  => $draftAddendum->addendum_order,
            'file_path'       => $filePath,
        ]);

    } catch (\Exception $e) {
        Log::error('handleSettlementForContract failed', [
            'transaction_id' => $transaction->id,
            'error'          => $e->getMessage(),
            'trace'          => $e->getTraceAsString(),
        ]);
    }
}

/**
 * Helper romanize untuk notificationHandler
 */
private function romanize(int $number): string
{
    $map = [
        'M'  => 1000, 'CM' => 900, 'D'  => 500, 'CD' => 400,
        'C'  => 100,  'XC' => 90,  'L'  => 50,  'XL' => 40,
        'X'  => 10,   'IX' => 9,   'V'  => 5,   'IV' => 4,
        'I'  => 1
    ];

    $result = '';
    foreach ($map as $roman => $value) {
        while ($number >= $value) {
            $result .= $roman;
            $number -= $value;
        }
    }
    return $result;
}

public function index()
{
    // Ambil semua transaksi user yang login
    $transactions = Transaction::where('user_id', Auth::id())
                               ->orderBy('created_at', 'desc')
                               ->get();

    // Total pendapatan
    $totalGross = $transactions->sum('gross_amount');

    // Total per room_type
    $grossByType = $transactions->groupBy('room_type')->map(function($items) {
        return $items->sum('gross_amount');
    });

    return view('layouts.dashboard.reward', compact('transactions', 'totalGross', 'grossByType'));
}

public function paymentFinish(Request $request)
    {
        Log::info('Payment Finish', $request->all());
        
        // Ambil order_id dari query parameter
        $orderId = $request->query('order_id');
        
        if ($orderId) {
            // Update status transaksi jika perlu
            $transaction = Transaction::where('order_id', $orderId)->first();
            
            if ($transaction) {
                // ✅ AUTO-LOGIN USER JIKA BELUM LOGIN
                if (!Auth::check() && $transaction->user_id) {
                    $user = User::find($transaction->user_id);
                    if ($user) {
                        Auth::login($user, true); // true = remember me
                        Log::info("User auto-logged in: {$user->id}");
                    }
                }
                
                // Cek status dari Midtrans
                $this->checkMidtransStatus($orderId);
            }
        }
        
        // Redirect ke dashboard mails dengan session message
        return redirect()->route('dashboard.mails')
            ->with('success', 'Pembayaran berhasil! Silakan cek status transaksi Anda.');
    }

    public function paymentError(Request $request)
    {
        Log::info('Payment Error', $request->all());
        
        $orderId = $request->query('order_id');
        
        // ✅ AUTO-LOGIN USER
        if ($orderId) {
            $transaction = Transaction::where('order_id', $orderId)->first();
            
            if ($transaction && !Auth::check() && $transaction->user_id) {
                $user = User::find($transaction->user_id);
                if ($user) {
                    Auth::login($user, true);
                    Log::info("User auto-logged in: {$user->id}");
                }
            }
        }
        
        return redirect()->route('dashboard.mails')
            ->with('error', 'Terjadi kesalahan dalam proses pembayaran. Silakan coba lagi.');
    }

    public function paymentUnfinish(Request $request)
    {
        Log::info('Payment Unfinish', $request->all());
        
        $orderId = $request->query('order_id');
        
        // ✅ AUTO-LOGIN USER
        if ($orderId) {
            $transaction = Transaction::where('order_id', $orderId)->first();
            
            if ($transaction && !Auth::check() && $transaction->user_id) {
                $user = User::find($transaction->user_id);
                if ($user) {
                    Auth::login($user, true);
                    Log::info("User auto-logged in: {$user->id}");
                }
            }
        }
        
        return redirect()->route('dashboard.mails')
            ->with('warning', 'Pembayaran belum selesai. Anda dapat melanjutkan pembayaran nanti.');
    }

    // Helper method untuk cek status
    private function checkMidtransStatus($orderId)
    {
        try {
            // ✅ Tambahkan config Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            
            $status = \Midtrans\Transaction::status($orderId);
            
            $transaction = Transaction::where('order_id', $orderId)->first();
            
            if ($transaction) {
                $transaction->status = $status->transaction_status;
                $transaction->payment_type = $status->payment_type ?? null;
                $transaction->save();
                
                Log::info("Transaction status updated: {$orderId} - {$status->transaction_status}");
            }
        } catch (\Exception $e) {
            Log::error('Midtrans status check failed: ' . $e->getMessage());
        }
    }
    
    // badge notification in home
         public function show($id)
{
    // transaksi yang ingin dibuka
    $focusedTransaction = Transaction::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    // tandai sebagai read
    if (!$focusedTransaction->is_read) {
        $focusedTransaction->update(['is_read' => true]);
    }

    // ✅ AMBIL SEMUA TRANSAKSI (seperti halaman index, agar layout tidak berubah!)
    $allTransactions = Transaction::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    return view('layouts.dashboard.mails', [
        'transactions' => $allTransactions,        // ✅ Layout aman (tetap list)
        'focusedTransaction' => $focusedTransaction, // ✅ yang ini dipakai tombol Snap
        'unreadTransactions' => Transaction::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count(),
    ]);
}


}
