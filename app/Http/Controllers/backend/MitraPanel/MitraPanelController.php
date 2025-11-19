<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\Location;
use App\Models\Mitra;
use App\Models\ServiceCategory;
use App\Models\ServicePrice;
use Barryvdh\DomPDF\Facade\Pdf;


class MitraPanelController extends Controller
{
        public function index(Request $request, $slug)
    {
        try {
            $user = auth()->user();
            $mitraId = $user->mitra_id;
            
            // VALIDASI: Pastikan user memiliki mitra_id
            if (!$mitraId) {
                return redirect()->route('mitrapanel.dashboard')
                    ->with('error', 'User tidak terasosiasi dengan mitra');
            }

            // ✅ CEK DULU: Apakah mitra memiliki locations?
            $mitraLocations = \App\Models\Location::where('mitra_id', $mitraId)->get();
            
            if ($mitraLocations->isEmpty()) {
                return redirect()->route('mitrapanel.dashboard')
                    ->with('error', 'Anda belum memiliki lokasi mitra');
            }

            // ✅ MODIFIED: Filter location berdasarkan mitra_id dan slug
            $location = $mitraLocations->first(function ($loc) use ($slug) {
                return Str::slug($loc->name) === $slug;
            });

            // Jika location tidak ditemukan, redirect ke lokasi pertama milik mitra
            if (!$location) {
                $firstLocation = $mitraLocations->first();
                return redirect()->route('mitrapanel.lokasi.detail', [
                    'slug' => Str::slug($firstLocation->name),
                    'period' => $request->get('period', 'monthly')
                ])->with('info', 'Dialihkan ke lokasi pertama milik Anda');
            }

            // Get period from request
            $period = $request->get('period', 'monthly');
            $dateRange = $this->getDateRange($period);

            // ✅ MODIFIED: Ambil lokasi HANYA milik mitra dengan hitungan transaksi
            $locations = $mitraLocations->map(function($loc) use ($dateRange) {
                $transactionCount = $loc->transactions()
                    ->whereIn('status', ['settlement', 'capture', 'paid'])
                    ->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']])
                    ->count();
                    
                return [
                    'slug' => Str::slug($loc->name),
                    'name' => $loc->name,
                    'id' => $loc->id,
                    'transactionCount' => $transactionCount,
                    'mitra_id' => $loc->mitra_id
                ];
            });

            // ✅ Format selectedLocation dengan hitungan SESUAI PERIODE
            $selectedLocation = [
                'slug' => Str::slug($location->name),
                'name' => $location->name,
                'id' => $location->id,
                'transactionCount' => $location->transactions()
                    ->whereIn('status', ['settlement', 'capture', 'paid'])
                    ->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']])
                    ->count(),
                'mitra_id' => $location->mitra_id
            ];

            return view('layouts.mitrapanel.lokasi-detail', compact(
                'locations',
                'location',
                'selectedLocation',
                'period'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            
            return redirect()->route('mitrapanel.dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * API untuk mendapatkan data service berdasarkan lokasi dan periode
     */
        public function getData(Request $request)
    {
        try {
            $locationId = $request->get('location_id');
            $period = $request->get('period', 'monthly');

            Log::info('getData called', [
                'location_id' => $locationId,
                'period' => $period
            ]);

            // Validasi location_id
            if (!$locationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location ID is required'
                ], 400);
            }

            // Tentukan rentang tanggal berdasarkan periode
            $dateRange = $this->getDateRange($period);
            
            Log::info('Date range', $dateRange);

            // ✅ Mapping icon dan type berdasarkan room_type
            $serviceMapping = [
                'Private Office' => ['icon' => '🏢', 'type' => 'private-office'],
                'Virtual Office' => ['icon' => '💼', 'type' => 'virtual-office'],
                'Sharing Room' => ['icon' => '👥', 'type' => 'sharing-room'],
                'Meeting Room' => ['icon' => '📊', 'type' => 'meeting-room'],
                'Big Meeting' => ['icon' => '🏛️', 'type' => 'big-meeting'],
                'Event Space' => ['icon' => '🎉', 'type' => 'event-space'],
                'Coworking Space' => ['icon' => '💻', 'type' => 'coworking-space'],
            ];

            // ✅ Query TANPA JOIN service_categories, langsung groupBy room_type
            $servicesData = DB::table('transactions')
                ->where('transactions.location_id', $locationId)
                ->whereIn('transactions.status', ['settlement', 'capture', 'paid'])
                ->whereBetween('transactions.booking_date', [$dateRange['start'], $dateRange['end']])
                ->whereNotNull('transactions.room_type') // Pastikan room_type tidak null
                ->select(
                    'transactions.room_type as name',
                    DB::raw('COUNT(transactions.id) as transactions'),
                    DB::raw('COALESCE(SUM(transactions.gross_amount), 0) as total'),
                    DB::raw('COALESCE(AVG(transactions.gross_amount), 0) as average'),
                    DB::raw('COALESCE(MAX(transactions.gross_amount), 0) as highest')
                )
                ->groupBy('transactions.room_type')
                ->get()
                ->map(function($item, $index) use ($serviceMapping) {
                    // Ambil mapping berdasarkan room_type
                    $mapping = $serviceMapping[$item->name] ?? ['icon' => '📦', 'type' => Str::slug($item->name)];
                    
                    return [
                        'id' => $index + 1, // ID dummy karena tidak ada service_category_id
                        'name' => $item->name,
                        'icon' => $mapping['icon'],
                        'type' => $mapping['type'],
                        'transactions' => (int) $item->transactions,
                        'total' => (float) $item->total,
                        'average' => (float) $item->average,
                        'highest' => (float) $item->highest,
                    ];
                });

            Log::info('Services data fetched', ['count' => $servicesData->count()]);

            // Hitung summary
            $totalOmzet = $servicesData->sum('total');
            $totalPajak = $totalOmzet * 0.1;

            // Cek apakah tabel tax_reports ada
            $taxReportExists = DB::select("SHOW TABLES LIKE 'tax_reports'");
            $taxReport = null;
            
            if (!empty($taxReportExists)) {
                $taxReport = DB::table('tax_reports')
                    ->where('location_id', $locationId)
                    ->where('period', $period)
                    ->where('year', date('Y'))
                    ->where('month', date('m'))
                    ->first();
            }

            $summary = [
                'totalOmzet' => $totalOmzet,
                'totalPajak' => $totalPajak,
                'status' => $taxReport ? 'reported' : 'not-reported',
                'reportDate' => $taxReport ? Carbon::parse($taxReport->reported_at)->format('d/m/Y') : null
            ];

            return response()->json([
                'success' => true,
                'services' => $servicesData,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper untuk mendapatkan rentang tanggal berdasarkan periode
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'daily':
                return [
                    'start' => $now->copy()->startOfDay()->toDateString(),
                    'end' => $now->copy()->endOfDay()->toDateString()
                ];
            
            case 'monthly':
                return [
                    'start' => $now->copy()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->endOfMonth()->toDateString()
                ];
            
            case 'yearly':
                return [
                    'start' => $now->copy()->startOfYear()->toDateString(),
                    'end' => $now->copy()->endOfYear()->toDateString()
                ];
            
            default:
                return [
                    'start' => $now->copy()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->endOfMonth()->toDateString()
                ];
        }
    }

    /**
     * API untuk melaporkan pajak
     */
    public function reportTax(Request $request)
    {
        try {
            $request->validate([
                'location_id' => 'required|exists:locations,id',
                'period' => 'required|in:daily,monthly,yearly',
                'total_omzet' => 'required|numeric',
                'total_pajak' => 'required|numeric'
            ]);

            // Cek apakah tabel tax_reports ada
            $taxReportExists = DB::select("SHOW TABLES LIKE 'tax_reports'");
            
            if (empty($taxReportExists)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabel tax_reports belum dibuat. Silakan jalankan migration terlebih dahulu.'
                ], 500);
            }

            // Insert ke tabel tax_reports
            DB::table('tax_reports')->insert([
                'location_id' => $request->location_id,
                'period' => $request->period,
                'year' => date('Y'),
                'month' => date('m'),
                'total_omzet' => $request->total_omzet,
                'total_pajak' => $request->total_pajak,
                'reported_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pajak berhasil dilaporkan',
                'reportDate' => now()->format('d/m/Y')
            ]);

        } catch (\Exception $e) {
            Log::error('Error in reportTax: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melaporkan pajak',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            \Log::info('=== PDF EXPORT START ===');
            
            // Ambil parameter filter
            $locationId = $request->get('location_id', 0);
            $period = $request->get('period', 'monthly');
            
            \Log::info("PDF Export - Location: {$locationId}, Period: {$period}");

            // Gunakan method yang sudah ada - getDashboardData()
            $dashboardResponse = $this->getDashboardData($request);
            $data = json_decode($dashboardResponse->getContent(), true);
            
            \Log::info('Dashboard data response:', ['success' => $data['success'] ?? false]);

            if (!$data['success']) {
                \Log::error('PDF Export Failed - Dashboard data error: ' . ($data['message'] ?? 'Unknown error'));
                return back()->with('error', 'Gagal mengambil data untuk export PDF');
            }

            // Siapkan data untuk view
            $exportData = [
                'stats' => $data['stats'],
                'recentTransactions' => $data['recentTransactions'],
                'period' => $period,
                'locationId' => $locationId,
                'locations' => $this->getLocationsForExport(),
                'generatedAt' => now()->format('d/m/Y H:i:s')
            ];

            \Log::info('PDF Data prepared', [
                'transactions' => count($exportData['recentTransactions']),
                'stats' => $exportData['stats']
            ]);

            // Gunakan path yang benar
            $viewPath = 'layouts.mitrapanel.exports.dashboard-pdf';
            
            \Log::info('Using view path: ' . $viewPath);
            
            // Validasi view sebelum generate PDF
            if (!view()->exists($viewPath)) {
                \Log::error('PDF View not found: ' . $viewPath);
                return back()->with('error', 'Template PDF tidak ditemukan');
            }

            // Generate PDF dengan path yang benar
            $pdf = \PDF::loadView($viewPath, $exportData);
            
            $filename = "dashboard-report-{$period}-" . date('Y-m-d') . ".pdf";

            \Log::info('=== PDF EXPORT SUCCESS ===');
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('=== PDF EXPORT ERROR ===');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk mendapatkan data lokasi untuk export
     */
    private function getLocationsForExport()
    {
        try {
            $locations = \App\Models\Location::with('city')->get();
            
            return $locations->map(function($loc) {
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'city' => $loc->city->name ?? 'N/A',
                    'address' => $loc->address
                ];
            })->toArray();
            
        } catch (\Exception $e) {
            \Log::error('Error in getLocationsForExport: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Ambil parameter filter
            $locationId = $request->get('location_id', 0);
            $period = $request->get('period', 'monthly');
            
            \Log::info("Excel Export - Location: {$locationId}, Period: {$period}");

            // Gunakan method yang sudah ada - getDashboardData()
            $dashboardResponse = $this->getDashboardData($request);
            $data = json_decode($dashboardResponse->getContent(), true);
            
            if (!$data['success']) {
                \Log::error('Excel Export Failed - Dashboard data error');
                return back()->with('error', 'Gagal mengambil data untuk export Excel');
            }

            $filename = "dashboard-report-{$period}-" . date('Y-m-d') . ".csv";
            
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use ($data, $period, $locationId) {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['DASHBOARD REPORT - ' . strtoupper($period)]);
                fputcsv($file, []); // Empty row
                
                // Summary Stats
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Transaksi', $data['stats']['totalTransactions']]);
                fputcsv($file, ['Total Revenue', 'Rp ' . number_format($data['stats']['totalRevenue'], 0, ',', '.')]);
                fputcsv($file, ['Lokasi Aktif', $data['stats']['activeLocations']]);
                fputcsv($file, ['Booking Pending', $data['stats']['pendingBookings']]);
                fputcsv($file, []); // Empty row
                
                // Recent Transactions
                if (!empty($data['recentTransactions'])) {
                    fputcsv($file, ['RECENT TRANSACTIONS']);
                    fputcsv($file, ['Tanggal', 'Layanan', 'Customer', 'Nominal', 'Status', 'Lokasi']);
                    
                    foreach ($data['recentTransactions'] as $transaction) {
                        fputcsv($file, [
                            $transaction['date'],
                            $transaction['service'],
                            $transaction['customer'],
                            'Rp ' . number_format($transaction['amount'], 0, ',', '.'),
                            $transaction['status'],
                            $transaction['location_name']
                        ]);
                    }
                }
                
                fputcsv($file, []); // Empty row
                fputcsv($file, ['Generated on', now()->format('d/m/Y H:i:s')]);
                
                fclose($file);
            };

            \Log::info('=== EXCEL EXPORT SUCCESS ===');
            
            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate Excel: ' . $e->getMessage());
        }
    }

        /**
     * Export detail transaksi per layanan (service category)
     */
        public function exportServiceDetail(Request $request)
    {
        try {
            $locationId = $request->input('location_id');
            $period = $request->input('period', 'monthly');
            $roomType = $request->input('service_name'); // dikirim dari frontend

            Log::info('Export Service Detail Request', [
                'location_id' => $locationId,
                'period' => $period,
                'room_type' => $roomType
            ]);

            if (!$locationId || !$roomType) {
                return response()->json(['error' => 'Data tidak valid'], 400);
            }

            // Ambil lokasi
            $location = Location::find($locationId);
            if (!$location) {
                return response()->json(['error' => 'Lokasi tidak ditemukan'], 404);
            }

            // Rentang tanggal
            $dateRange = $this->getDateRange($period);

            // ✅ Query berdasarkan room_type
            $transactions = Transaction::where('location_id', $locationId)
                ->where('room_type', $roomType)
                ->whereIn('status', ['settlement', 'capture', 'paid'])
                ->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']])
                ->orderBy('booking_date', 'desc')
                ->get();

            Log::info('Transactions found', ['count' => $transactions->count()]);

            // Statistik
            $stats = [
                'total_transactions' => $transactions->count(),
                'total_amount' => $transactions->sum('gross_amount'),
                'average_amount' => $transactions->avg('gross_amount'),
                'highest_amount' => $transactions->max('gross_amount'),
                'lowest_amount' => $transactions->min('gross_amount'),
            ];

            $data = [
                'location' => $location,
                'room_type' => $roomType,
                'period' => $period,
                'period_label' => $this->getPeriodLabel($period),
                'date_range' => $dateRange,
                'transactions' => $transactions,
                'stats' => $stats,
                'generated_at' => now()->format('d/m/Y H:i:s')
            ];

            $pdf = Pdf::loadView('layouts.mitrapanel.exports.service-detail-pdf', $data);
            $pdf->setPaper('a4', 'portrait');

            $filename = "Detail_{$roomType}_{$location->name}_{$period}_" . date('Ymd') . ".pdf";

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error exporting service detail', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    
    /**
     * Get period label
     */
    private function getPeriodLabel($period)
    {
        switch ($period) {
            case 'daily':
                return 'Hari Ini - ' . Carbon::today()->format('d F Y');
            case 'monthly':
                return 'Bulan ' . Carbon::now()->format('F Y');
            case 'yearly':
                return 'Tahun ' . Carbon::now()->format('Y');
            default:
                return 'Bulan ' . Carbon::now()->format('F Y');
        }
    }

    /**
     * API untuk mendapatkan list lokasi dengan count transaksi settlement
     * MODIFIED: Filter berdasarkan mitra_id user yang login
     */
    public function getLocations()
    {
        try {
            // DAPATKAN MITRA_ID DARI USER YANG LOGIN
            $user = auth()->user();
            $mitraId = $user->mitra_id;
            
            // VALIDASI: Pastikan user memiliki mitra_id
            if (!$mitraId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terasosiasi dengan mitra'
                ], 400);
            }

            // FILTER LOKASI BERDASARKAN MITRA_ID
            $locations = Location::where('mitra_id', $mitraId) // ✅ TAMBAHKAN FILTER INI
                ->withCount(['transactions' => function($query) {
                    $query->whereIn('status', ['settlement', 'capture', 'paid']);
                }])
                ->with('city')
                ->get();

            return response()->json([
                'success' => true,
                'locations' => $locations->map(function($loc) {
                    return [
                        'id' => $loc->id,
                        'name' => $loc->name,
                        'slug' => Str::slug($loc->name),
                        'city' => $loc->city->name ?? '',
                        'address' => $loc->address,
                        'transaction_count' => $loc->transactions_count,
                        'mitra_id' => $loc->mitra_id // ✅ TAMBAHKAN UNTUK DEBUGGING
                    ];
                }),
                'debug' => [ // ✅ OPTIONAL: Untuk debugging
                    'user_id' => auth()->id(),
                    'mitra_id' => $mitraId,
                    'locations_count' => $locations->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getLocations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data lokasi'
            ], 500);
        }
    }

    /**
     * API untuk data dashboard dengan data REAL dari database
     * MODIFIED: Filter berdasarkan mitra_id user yang login
     */
    public function getDashboardData(Request $request)
    {
        try {
            $user = auth()->user();
            $mitraId = $user->mitra_id;
            $locationId = $request->get('location_id', 0);
            $period = $request->get('period', 'monthly');
            
            // VALIDASI mitra_id
            if (!$mitraId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terasosiasi dengan mitra'
                ], 400);
            }
            
            // Get date range berdasarkan periode
            $dateRange = $this->getDateRange($period);

            \Log::info("=== DASHBOARD DATA ===");
            \Log::info("Mitra ID: {$mitraId}, Period: {$period}, Location: {$locationId}");
            \Log::info("Date Range: {$dateRange['start']} to {$dateRange['end']}");

            // ✅ BASE QUERY dengan filter mitra_id
            $baseTransactionQuery = function($query) use ($mitraId, $locationId) {
                $query->whereHas('location', function($q) use ($mitraId) {
                    $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                });
                
                if ($locationId > 0) {
                    $query->where('location_id', $locationId);
                }
            };

            // ✅ TOTAL TRANSACTIONS: Settlement only dengan filter mitra
            $transactionQuery = Transaction::whereIn('status', ['settlement', 'capture', 'paid'])
                ->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']])
                ->whereHas('location', function($q) use ($mitraId) {
                    $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                });

            if ($locationId > 0) {
                $transactionQuery->where('location_id', $locationId);
            }

            $totalTransactions = $transactionQuery->count();
            $totalRevenue = $transactionQuery->sum('gross_amount');

            \Log::info("Transactions: {$totalTransactions}, Revenue: {$totalRevenue}");

            // ✅ ACTIVE LOCATIONS: Lokasi yang punya transaksi settlement + filter mitra
            $activeLocations = Location::where('mitra_id', $mitraId) // ✅ FILTER MITRA
                ->whereHas('transactions', function($query) use ($dateRange, $mitraId) {
                    $query->whereIn('status', ['settlement', 'capture', 'paid'])
                        ->whereBetween('booking_date', [$dateRange['start'], $dateRange['end']])
                        ->whereHas('location', function($q) use ($mitraId) {
                            $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                        });
                })
                ->when($locationId > 0, function($query) use ($locationId) {
                    $query->where('id', $locationId);
                })
                ->count();

            // ✅ PENDING BOOKINGS: Transaksi dengan status pending + filter mitra
            $pendingBookings = Transaction::where('status', 'pending')
                ->whereHas('location', function($q) use ($mitraId) {
                    $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                })
                ->when($locationId > 0, function($query) use ($locationId) {
                    $query->where('location_id', $locationId);
                })
                ->count();

            \Log::info("Active Locations: {$activeLocations}, Pending: {$pendingBookings}");

            // Recent transactions (5 terbaru) dengan filter mitra
            $recentTransactions = Transaction::whereIn('status', ['settlement', 'capture', 'paid'])
                ->whereHas('location', function($q) use ($mitraId) {
                    $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                })
                ->when($locationId > 0, function($query) use ($locationId) {
                    $query->where('location_id', $locationId);
                })
                ->with(['location'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($transaction) {
                    return [
                        'id' => $transaction->id,
                        'date' => Carbon::parse($transaction->booking_date)->format('d M Y'),
                        'service' => $transaction->room_type,
                        'customer' => $transaction->customer_name ?? 'Customer',
                        'amount' => (float) $transaction->gross_amount,
                        'status' => $this->normalizeTransactionStatus($transaction->status),
                        'location_name' => $transaction->location->name ?? 'N/A'
                    ];
                });

            // Chart data dengan filter mitra
            $chartData = $this->getRealChartData($period, $locationId, $dateRange, $mitraId); // ✅ PASS MITRA_ID

            $response = [
                'success' => true,
                'stats' => [
                    'totalTransactions' => $totalTransactions,
                    'totalRevenue' => (float) $totalRevenue,
                    'activeLocations' => $activeLocations,
                    'pendingBookings' => $pendingBookings
                ],
                'recentTransactions' => $recentTransactions,
                'chartData' => $chartData,
                'debug' => [ // ✅ OPTIONAL: Untuk debugging
                    'mitra_id' => $mitraId,
                    'location_id' => $locationId,
                    'period' => $period
                ]
            ];

            \Log::info("=== FINAL RESPONSE ===");
            \Log::info(json_encode($response));

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in getDashboardData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard'
            ], 500);
        }
    }

        private function normalizeTransactionStatus($status)
    {
        $statusMap = [
            // Status dari database
            'settlement' => 'success',
            'capture' => 'success', 
            'paid' => 'success',
            'pending' => 'pending',
            'expire' => 'failed'
        ];
        
        return $statusMap[strtolower($status)] ?? 'pending';
    }

    /**
     * Get REAL chart data dari database
     * MODIFIED: Filter berdasarkan mitra_id user yang login
     */
    private function getRealChartData($period, $locationId, $dateRange, $mitraId)
    {
        $chartData = [
            'daily' => ['labels' => [], 'data' => []],
            'monthly' => ['labels' => [], 'data' => []],
            'yearly' => ['labels' => [], 'data' => []]
        ];

        // ✅ BASE QUERY dengan filter mitra_id
        $baseQuery = function($query) use ($mitraId, $locationId) {
            $query->whereHas('location', function($q) use ($mitraId) {
                $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
            });
            
            if ($locationId > 0) {
                $query->where('location_id', $locationId);
            }
        };

        switch ($period) {
            case 'daily':
                // Data 7 hari terakhir - HANYA transaksi settlement
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();

                $data = Transaction::whereIn('status', ['settlement', 'capture', 'paid'])
                    ->whereBetween('booking_date', [$startDate, $endDate])
                    ->whereHas('location', function($q) use ($mitraId) {
                        $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                    })
                    ->when($locationId > 0, function($query) use ($locationId) {
                        $query->where('location_id', $locationId);
                    })
                    ->select(
                        DB::raw('DATE(booking_date) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                \Log::info("📊 Daily Chart Data - Mitra: {$mitraId}, Records: " . $data->count());

                // Format untuk chart - 7 hari terakhir
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i)->format('Y-m-d');
                    $dayLabel = Carbon::now()->subDays($i)->format('d M');
                    
                    $count = $data->firstWhere('date', $date);
                    $chartData['daily']['labels'][] = $dayLabel;
                    $chartData['daily']['data'][] = $count ? $count->count : 0;
                }
                
                \Log::info("📊 Daily Chart Final - Labels: " . json_encode($chartData['daily']['labels']));
                \Log::info("📊 Daily Chart Final - Data: " . json_encode($chartData['daily']['data']));
                break;

            case 'monthly':
                // Data 12 bulan terakhir - HANYA transaksi settlement
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();

                $data = Transaction::whereIn('status', ['settlement', 'capture', 'paid'])
                    ->whereBetween('booking_date', [$startDate, $endDate])
                    ->whereHas('location', function($q) use ($mitraId) {
                        $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                    })
                    ->when($locationId > 0, function($query) use ($locationId) {
                        $query->where('location_id', $locationId);
                    })
                    ->select(
                        DB::raw('YEAR(booking_date) as year'),
                        DB::raw('MONTH(booking_date) as month'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get();

                \Log::info("📊 Monthly Chart Data - Mitra: {$mitraId}, Records: " . $data->count());

                $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $year = $date->year;
                    $month = $date->month;
                    
                    $count = $data->first(function($item) use ($year, $month) {
                        return $item->year == $year && $item->month == $month;
                    });
                    
                    $chartData['monthly']['labels'][] = $monthLabels[$month - 1];
                    $chartData['monthly']['data'][] = $count ? $count->count : 0;
                }
                
                \Log::info("📊 Monthly Chart Final - Labels: " . json_encode($chartData['monthly']['labels']));
                \Log::info("📊 Monthly Chart Final - Data: " . json_encode($chartData['monthly']['data']));
                break;

            case 'yearly':
                // Data 5 tahun terakhir - HANYA transaksi settlement
                $startDate = Carbon::now()->subYears(4)->startOfYear();
                $endDate = Carbon::now()->endOfYear();

                $data = Transaction::whereIn('status', ['settlement', 'capture', 'paid'])
                    ->whereBetween('booking_date', [$startDate, $endDate])
                    ->whereHas('location', function($q) use ($mitraId) {
                        $q->where('mitra_id', $mitraId); // ✅ FILTER MITRA
                    })
                    ->when($locationId > 0, function($query) use ($locationId) {
                        $query->where('location_id', $locationId);
                    })
                    ->select(
                        DB::raw('YEAR(booking_date) as year'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('year')
                    ->orderBy('year')
                    ->get();

                \Log::info("📊 Yearly Chart Data - Mitra: {$mitraId}, Records: " . $data->count());

                for ($i = 4; $i >= 0; $i--) {
                    $year = Carbon::now()->subYears($i)->year;
                    $count = $data->firstWhere('year', $year);
                    $chartData['yearly']['labels'][] = $year;
                    $chartData['yearly']['data'][] = $count ? $count->count : 0;
                }
                
                \Log::info("📊 Yearly Chart Final - Labels: " . json_encode($chartData['yearly']['labels']));
                \Log::info("📊 Yearly Chart Final - Data: " . json_encode($chartData['yearly']['data']));
                break;
        }

        return $chartData;
    }
    
    
}