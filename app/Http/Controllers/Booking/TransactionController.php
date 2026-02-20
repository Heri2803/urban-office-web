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


class TransactionController extends Controller
{
    public function __construct()
    {
        try {
            // Load konfigurasi Midtrans dari config/midtrans.php
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
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

    private function calculateAmount(string $roomType, array $params): int
    {
        $amount = 0;

        try {
            switch ($roomType) {
                case 'Virtual Office':
                    if (isset($params['paket'])) {
                        if ($params['paket'] === 'monthly') {
                            $amount = ($params['bulan'] ?? 1) * 1000000; // contoh: 1 jt / bulan
                        } elseif ($params['paket'] === 'yearly') {
                            $amount = ($params['tahun'] ?? 1) * 10000000; // contoh: 10 jt / tahun
                        }
                    }
                    break;

                case 'Event Space':
                    $amount = ($params['jumlah_orang'] ?? 0) * 100000; // contoh: 100rb / orang
                    break;

                case 'Meeting Room':
                    $amount = ($params['jumlah_orang'] ?? 0) * ($params['jam'] ?? 1) * 50000; // contoh: 50rb / orang per jam
                    break;

                case 'Coworking Space':
                    $amount = ($params['jumlah_orang'] ?? 0) * 75000; // contoh: 75rb / orang
                    break;

                default:
                    throw new Exception('Invalid room type: ' . $roomType);
            }

            // Minimum amount validation
            if ($amount <= 0) {
                throw new Exception('Invalid amount calculated: ' . $amount);
            }

        } catch (Exception $e) {
            Log::error('Amount calculation error: ' . $e->getMessage(), $params);
            throw $e;
        }

        return $amount;
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

            'subtotal'      => 'required|numeric|min:0',
            'admin_fee'     => 'required|numeric|min:0',
            'deposit'       => 'required|numeric|min:0',
            'total_amount'  => 'required|numeric|min:0',

            'lunches' => 'nullable|array',
            'lunches.*.lunch_option_id' => 'required_with:lunches|exists:lunch_options,id',
            'lunches.*.quantity' => 'required_with:lunches|integer|min:1',
            'lunch_total' => 'nullable|numeric|min:0'
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
            'start_time'   => $validated['start_time'] ?? null,

            'paket'        => $validated['paket'],
            'jam'          => $duration['jam'],
            'hari'         => $duration['hari'],
            'minggu'       => $duration['minggu'],
            'bulan'        => $duration['bulan'],
            'tahun'        => $duration['tahun'],

            'service_category_id' => $validated['service_category_id'] ?? null,
            'status_pkp'   => $validated['status_pkp'] ?? 'Non PKP',

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
                $unitPrice = (int) round($lunchOption->price); // HARGA DARI DB

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

        // ✅ VALIDASI FINAL AMOUNT
        $expectedTotal = $serverSideSubtotal + $deposit;
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
        \Midtrans\Config::$overrideNotifUrl = 'https://bacf-118-99-123-11.ngrok-free.app/midtrans/notification';

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
                'name'     => $room_name,
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
            DB::commit();
            
            Log::info('Transaction updated successfully', [
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $transaction->status
            ]);
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
