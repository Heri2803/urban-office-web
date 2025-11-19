<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    try {
        Log::info('Booking request received', $request->all());

        // ✅ VALIDASI INPUT - NULLABLE untuk field yang tidak selalu dikirim
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
        ]);

        $userId = Auth::id();
        $amount = (int) $validated['total_amount'];
        $orderId = 'ORDER-' . strtoupper(Str::random(10)) . '-' . time();

        // ✅ NORMALISASI DURASI
        $duration = [
            'jam'    => null,
            'hari'   => null,
            'minggu' => null,
            'bulan'  => null,
            'tahun'  => null,
        ];

        switch ($validated['paket']) {
            case 'hourly':  $duration['jam']    = $validated['jam'] ?? null; break;
            case 'daily':   $duration['hari']   = $validated['hari'] ?? null; break;
            case 'weekly':  $duration['minggu'] = $validated['minggu'] ?? null; break;
            case 'monthly': $duration['bulan']  = $validated['bulan'] ?? null; break;
            case 'yearly':  $duration['tahun']  = $validated['tahun'] ?? null; break;
        }

        // ✅ VALIDASI durasi tidak boleh kosong
        $durationValue = $duration['jam'] ?? $duration['hari'] ?? $duration['minggu'] ?? $duration['bulan'] ?? $duration['tahun'];
        if (empty($durationValue)) {
            return response()->json([
                'success' => false,
                'message' => 'Durasi pemesanan tidak valid',
            ], 422);
        }

      // ✅ BENAR - Cek dulu apakah key exists
        $room = !empty($validated['room_id']) ? Room::find($validated['room_id']) : null;
        $room_name = $room ? "Ruang " . $room->room_number : $validated['room_type'];

        // ✅ SIMPAN TRANSAKSI
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

            'deposit'      => (int) $validated['deposit'],
            'gross_amount' => $amount,

            'order_id'     => $orderId,
            'payment_type' => null,
            'status'       => 'pending',
            'snap_token'   => null,
            'transaction_time' => now(),
            'is_read'      => false,
        ]);

        Log::info('Transaction created', ['transaction_id' => $transaction->id , 'is_read' => $transaction->is_read]);

        // ✅ BUAT LABEL DURASI
        $duration_label = $this->generateDurationLabel($validated['paket'], $duration);

        // ✅ Hitung item price (gross_amount - deposit)
        $itemPrice = $amount - (int) $validated['deposit'];
        
        // ✅ Pastikan tidak ada nilai negatif
        if ($itemPrice < 0) {
            $itemPrice = 0;
        }

        // ✅ === PARAM MIDTRANS ===
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [],
        ];

        // ✅ Item booking dengan nama yang lebih pendek (max 50 karakter)
        $paketLabel = ucfirst($validated['paket']);
        $bookingName = "{$validated['room_type']} - {$room_name}";
        $durationName = "Paket {$paketLabel} ({$duration_label})";
        
        if ($itemPrice > 0) {
            $params['item_details'][] = [
                'id'       => 'room_booking',
                'price'    => $itemPrice,
                'quantity' => 1,
                'name'     => substr($bookingName, 0, 50), // Batasi 50 karakter
            ];
            
            // Tambahkan durasi sebagai item terpisah
            $params['item_details'][] = [
                'id'       => 'duration_info',
                'price'    => 0,
                'quantity' => 1,
                'name'     => $durationName,
            ];
        }

        // ✅ Tambahkan deposit jika ada
        if ($transaction->deposit > 0) {
            $params['item_details'][] = [
                'id'       => 'deposit',
                'price'    => (int) $transaction->deposit,
                'quantity' => 1,
                'name'     => 'Deposit',
            ];
        }

        // ✅ INFORMASI BOOKING (dengan quantity 1 dan price 0)
        $params['item_details'][] = [
            'id'       => 'info_header',
            'price'    => 0,
            'quantity' => 1,
            'name'     => '== Info Booking ==',
        ];

        // ✅ Tanggal Booking
        if (!empty($transaction->booking_date)) {
            $params['item_details'][] = [
                'id'       => 'booking_date',
                'price'    => 0,
                'quantity' => 1,
                'name'     => 'Tanggal: ' . \Carbon\Carbon::parse($transaction->booking_date)->format('d F Y'),
            ];
        }

        // ✅ Jam Mulai (untuk hourly/daily)
        if (!empty($transaction->start_time) && in_array($validated['paket'], ['hourly', 'daily'])) {
            $params['item_details'][] = [
                'id'       => 'start_time',
                'price'    => 0,
                'quantity' => 1,
                'name'     => 'Jam Mulai: ' . $transaction->start_time,
            ];
        }

        // ✅ Jumlah Orang
        if (!empty($transaction->jumlah_orang)) {
            $params['item_details'][] = [
                'id'       => 'total_people',
                'price'    => 0,
                'quantity' => 1,
                'name'     => 'Jumlah Orang: ' . $transaction->jumlah_orang . ' orang',
            ];
        }

        // ✅ INFORMASI PEMESAN
        $params['item_details'][] = [
            'id'       => 'customer_header',
            'price'    => 0,
            'quantity' => 1,
            'name'     => '== Info Pemesan ==',
        ];

        $params['item_details'][] = [
            'id'       => 'customer_name',
            'price'    => 0,
            'quantity' => 1,
            'name'     => 'Nama: ' . $transaction->nama_lengkap,
        ];

        $params['item_details'][] = [
            'id'       => 'customer_email',
            'price'    => 0,
            'quantity' => 1,
            'name'     => 'Email: ' . $transaction->email,
        ];

        if (!empty($transaction->phone)) {
            $params['item_details'][] = [
                'id'       => 'customer_phone',
                'price'    => 0,
                'quantity' => 1,
                'name'     => 'No HP: ' . $transaction->phone,
            ];
        }

        // ✅ CUSTOMER DETAILS
        $params['customer_details'] = [
            'first_name' => $transaction->nama_lengkap,
            'email'      => $transaction->email,
            'phone'      => $transaction->phone ?? '',
        ];

        // ✅ CALLBACK
        $params['callbacks'] = [
            'finish'  => route('payment.finish') . '?order_id=' . $orderId,
            'error'   => route('payment.error') . '?order_id=' . $orderId,
            'pending' => route('payment.unfinish') . '?order_id=' . $orderId,
        ];

        Log::info('Midtrans params prepared', ['params' => $params]);

        
        // ✅ BUAT TRANSAKSI DI MIDTRANS (PERBAIKAN PALING PENTING)
        $midtrans = \Midtrans\Snap::createTransaction($params);
        
        // ✅ SIMPAN TOKEN & REDIRECT URL
        $transaction->update([
            "snap_token"   => $midtrans->token,
            "redirect_url" => $midtrans->redirect_url,
        ]);
        
        Log::info("Snap Token Generated", [
            "order_id" => $orderId,
            "token"    => $midtrans->token
        ]);
        
        return response()->json([
            'success'     => true,
            'message'     => 'Transaksi berhasil dibuat',
            'snap_token'  => $midtrans->token, // ✅ PERBAIKAN DI SINI
            'redirect_url'=> $midtrans->redirect_url,
            'order_id'    => $orderId,
            'transaction' => $transaction,
        ]);


    } catch (ValidationException $e) {
        Log::error('Validation error', ['errors' => $e->errors()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $e->errors(),
        ], 422);

    } catch (Exception $e) {
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
    \Midtrans\Config::$serverKey    = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized  = true;
    \Midtrans\Config::$is3ds        = true;

    try {
        // ✅ Gunakan Notification resmi Midtrans
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $orderId           = $notif->order_id;
        $paymentType       = $notif->payment_type;
        $fraudStatus       = $notif->fraud_status ?? null;

        if (!$orderId) {
            return response()->json(['error' => 'Order ID tidak ditemukan'], 400);
        }

        $transaction = Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        // ✅ Update status berdasarkan status Midtrans
        switch ($transactionStatus) {

            case 'capture':
                if ($paymentType == 'credit_card') {
                    $transaction->status = ($fraudStatus == 'challenge')
                        ? 'challenge'
                        : 'settlement';
                }
                break;

            case 'settlement':
                $transaction->status = 'settlement';
                break;

            case 'pending':
                $transaction->status = 'pending';
                break;

            case 'deny':
                $transaction->status = 'deny';
                break;

            case 'cancel':
                $transaction->status = 'cancel';
                break;

            case 'expire':
                $transaction->status = 'expire';
                break;

            default:
                $transaction->status = 'pending';
        }

        // ✅ Simpan payment type
        $transaction->payment_type = $paymentType;
        $transaction->save();

        return response()->json([
            'success' => true,
            'status'  => $transaction->status
        ]);

    } catch (\Exception $e) {

        // ✅ Logging tetap mempertahankan gaya coding Anda
        Log::error(
            'Midtrans notification error: ' . $e->getMessage(),
            $request->all() // seperti versi Anda sebelumnya
        );

        return response()->json(['error' => 'Terjadi kesalahan server'], 500);
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
