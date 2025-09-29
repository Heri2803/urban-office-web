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

    public function store(Request $request)
{
    try {
        // Log request untuk debugging
        Log::info('Booking request received', $request->all());

        // Validasi input
        $validated = $request->validate([
            'city'          => 'required|string|max:255',
            'room_type'     => 'required|string|in:Virtual Office,Event Space,Meeting Room,Coworking Space',
            'booking_date'  => 'nullable|date|after_or_equal:today',
            'jumlah_orang'  => 'nullable|integer|min:1|max:1000',
            'paket'         => 'nullable|in:monthly,yearly',
            'bulan'         => 'nullable|integer|min:1|max:12',
            'tahun'         => 'nullable|integer|min:1|max:5',
            'jam'           => 'nullable|integer|min:1|max:24',
            'status_pkp'    => 'nullable|in:Non PKP,PKP',
            'phone'         => 'nullable|string|max:20',
            'nama_lengkap'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
        ]);

        // ✅ Ambil ID user yang login, fallback ke null (guest)
        $userId = Auth::id(); // Kalau user login, ini terisi. Kalau tidak, null.

        // Hitung nominal
        $amount = $this->calculateAmount($validated['room_type'], $validated);

        // Buat order_id unik
        $orderId = 'ORDER-' . strtoupper(Str::random(10)) . '-' . time();

        // Simpan transaksi ke database
        $transaction = Transaction::create([
            'user_id'          => $userId, // ✅ Now properly defined
            'city'             => $validated['city'],
            'room_type'        => $validated['room_type'],
            'jumlah_orang'     => $validated['jumlah_orang'] ?? null,
            'booking_date'     => $validated['booking_date'] ?? null,
            'paket'            => $validated['paket'] ?? null,
            'bulan'            => $validated['bulan'] ?? null,
            'tahun'            => $validated['tahun'] ?? null,
            'jam'              => $validated['jam'] ?? null,
            'status_pkp'       => $validated['status_pkp'] ?? null,
            'phone'            => $validated['phone'] ?? null,
            'nama_lengkap'     => $validated['nama_lengkap'],
            'email'            => $validated['email'],
            'order_id'         => $orderId,
            'gross_amount'     => $amount,
            'payment_type'     => null,
            'status'           => 'pending',
            'snap_token'       => null,
            'transaction_time' => now(),
        ]);

        // Parameter untuk Midtrans Snap
        $params = [
            'transaction_details' => [
                'order_id'      => $transaction->order_id,
                'gross_amount'  => $transaction->gross_amount,
            ],
            'customer_details' => [
                'first_name'    => $transaction->nama_lengkap,
                'email'         => $transaction->email,
                'phone'         => $transaction->phone ?? '',
            ]
        ];

        // Generate Snap Token
        $snapToken = Snap::getSnapToken($params);

        if (!$snapToken) {
            throw new Exception('Failed to generate Snap token');
        }

        // Update token ke transaksi
        $transaction->update(['snap_token' => $snapToken]);

        Log::info('Transaction created successfully', ['order_id' => $orderId]);

        return response()->json([
            'success'     => true,
            'message'     => 'Transaksi berhasil dibuat',
            'snap_token'  => $snapToken,
            'order_id'    => $orderId,
            'amount'      => $amount,
            'transaction' => $transaction,
        ], 200);

    } catch (ValidationException $e) {
        Log::warning('Validation error', ['errors' => $e->errors()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $e->errors(),
        ], 422);

    } catch (Exception $e) {
        Log::error('Transaction creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            'error'   => config('app.debug') ? $e->getMessage() : 'Internal server error',
        ], 500);
    }
}

public function notificationHandler(Request $request)
{
    // Pastikan load Midtrans Config
    \Midtrans\Config::$serverKey    = config('midtrans.server_key');
    \Midtrans\Config::$isProduction = config('midtrans.is_production');
    \Midtrans\Config::$isSanitized  = true;
    \Midtrans\Config::$is3ds        = true;

    // Ambil JSON dari request body
    $notif = $request->all();

    try {
        $transactionStatus = $notif['transaction_status'] ?? null;
        $orderId           = $notif['order_id'] ?? null;
        $paymentType       = $notif['payment_type'] ?? null;

        if (!$orderId) {
            return response()->json(['error' => 'Order ID tidak ditemukan'], 400);
        }

        $transaction = \App\Models\Transaction::where('order_id', $orderId)->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update status berdasarkan status dari Midtrans
        switch ($transactionStatus) {
            case 'capture':
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

        $transaction->payment_type = $paymentType;
        $transaction->save();

        return response()->json(['success' => true, 'status' => $transaction->status]);
    } catch (\Exception $e) {
        \Log::error('Midtrans notification error: '.$e->getMessage(), $notif);
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

    
}