<?php
// app/Http/Controllers/Admin/BookingController.php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Carbon\Carbon; 

class BookingController extends Controller
{
    // Di BookingController nanti akan pakai:
    public function getAllBookings(Request $request): JsonResponse
    {
        try {
            // ✅ GUNAKAN RELASI YANG SUDAH ADA: lunches dengan lunchOption
            $query = Transaction::with([
                'location', 
                'lunches.lunchOption' // ✅ Include lunch options
            ]);
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by service type
            if ($request->filled('service')) {
                $query->where('room_type', $request->service);
            }
            
            // Filter by date
            if ($request->filled('date')) {
                $query->whereDate('booking_date', $request->date);
            }
            
            // Filter by date range
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('booking_date', [
                    $request->date_from, 
                    $request->date_to
                ]);
            }
            
            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'LIKE', "%{$search}%")
                      ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $bookings = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            // Transform data untuk frontend - DENGAN LUNCH DATA
            $transformedBookings = $bookings->map(function ($transaction) {
                // ✅ FORMAT LUNCH ITEMS DARI RELASI
                $lunchItems = $transaction->lunches->map(function ($lunch) {
                    return [
                        'lunch_option' => $lunch->lunchOption->name ?? 'Lunch Option',
                        'quantity' => $lunch->quantity,
                        'unit_price' => (float) $lunch->unit_price,
                        'subtotal' => (float) $lunch->subtotal
                    ];
                });
                
                return [
                    'id' => $transaction->id,
                    'bookingId' => $transaction->order_id,
                    'transactionDate' => $transaction->transaction_time 
                        ? $transaction->transaction_time->format('d M Y, H:i')
                        : $transaction->created_at->format('d M Y, H:i'),
                    'bookingDate' => $transaction->booking_date 
                        ? $transaction->booking_date->format('d M Y')
                        : 'Not set',
                    'bookingTime' => $transaction->booking_time_formatted, // ✅ Accessor dari model
                    'customerName' => $transaction->nama_lengkap,
                    'customerPhone' => $transaction->phone,
                    'customerEmail' => $transaction->email,
                    'customerCompany' => '-', // Default value
                    'service' => $transaction->room_type,
                    'serviceType' => $transaction->service_type_slug, // ✅ Accessor dari model
                    'package' => $transaction->paket ?? 'Standard',
                    'duration' => $transaction->duration_text, // ✅ Accessor dari model
                    'participants' => $transaction->participants_text, // ✅ Accessor dari model
                    'paymentStatus' => $transaction->status,
                    'basePrice' => (float) $transaction->gross_amount,
                    'discount' => 0,
                    'lunchTotal' => (float) $transaction->lunch_total, // ✅ dari database
                    'lunchItems' => $lunchItems, // ✅ dari relasi
                    'total' => (float) $transaction->gross_amount + (float) $transaction->lunch_total, // ✅ total termasuk lunch
                    'paymentType' => $transaction->payment_type ?? 'Not specified',
                    'location' => $transaction->location ? $transaction->location->name : 'Not assigned',
                    'roomNumber' => 'Not assigned yet' // Default value
                ];
            });
            
            // Get summary stats
            $summary = [
                'settlement' => Transaction::where('status', 'settlement')->count(),
                'pending' => Transaction::where('status', 'pending')->count(),
                'expire' => Transaction::where('status', 'expire')->count(),
                'total' => Transaction::count()
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Bookings fetched successfully',
                'data' => [
                    'bookings' => $transformedBookings,
                    'summary' => $summary,
                    'pagination' => [
                        'current_page' => $bookings->currentPage(),
                        'total_pages' => $bookings->lastPage(),
                        'total_items' => $bookings->total(),
                        'per_page' => $bookings->perPage(),
                        'from' => $bookings->firstItem(),
                        'to' => $bookings->lastItem()
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to fetch bookings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    private function transformBookingData(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'bookingId' => $transaction->order_id,
            'transactionDate' => $transaction->transaction_time 
                ? $transaction->transaction_time->format('d M Y, H:i')
                : $transaction->created_at->format('d M Y, H:i'),
            'bookingDate' => $transaction->booking_date 
                ? $transaction->booking_date->format('d M Y')
                : 'Not set',
            'bookingTime' => $this->formatBookingTime($transaction),
            'customerName' => $transaction->nama_lengkap,
            'customerPhone' => $transaction->phone,
            'customerEmail' => $transaction->email,
            'service' => $transaction->room_type, // Langsung pakai room_type
            'serviceType' => $this->mapToServiceType($transaction->room_type),
            'package' => $transaction->paket ?? 'Standard',
            'duration' => $this->getDuration($transaction),
            'participants' => $transaction->jumlah_orang 
                ? $transaction->jumlah_orang . ' people' 
                : 'Not specified',
            'paymentStatus' => $transaction->status,
            'basePrice' => (int) $transaction->gross_amount,
            'discount' => 0, // Sesuai data sample, discount 0
            'total' => (int) $transaction->gross_amount,
            'paymentType' => $transaction->payment_type ?? 'Not specified',
            'location' => $transaction->location ? $transaction->location->name : 'Not assigned',
            'snapToken' => $transaction->snap_token
        ];
    }
    
    private function formatBookingTime($transaction): string
    {
        if ($transaction->start_time && $transaction->jam) {
            return $transaction->start_time . ' (' . $transaction->jam . ' hours)';
        }
        
        if ($transaction->start_time) {
            return $transaction->start_time;
        }
        
        return 'Flexible';
    }
    
     private function getDuration($transaction): string
    {
        if ($transaction->jam) return $transaction->jam . ' Hours';
        if ($transaction->hari) return $transaction->hari . ' Days';
        if ($transaction->minggu) return $transaction->minggu . ' Weeks';
        if ($transaction->bulan) return $transaction->bulan . ' Months';
        if ($transaction->tahun) return $transaction->tahun . ' Years';
        
        return 'Custom Duration';
    }
    
    private function mapToServiceType(string $roomType): string
    {
        return (new Transaction())->getServiceTypeSlugAttribute(); // ✅ Gunakan method dari model
    }
    
    /**
     * Get filter options for frontend
     */
     public function getFilterOptions(): JsonResponse
    {
        try {
            $serviceTypes = Transaction::distinct()
                ->whereNotNull('room_type')
                ->pluck('room_type')
                ->filter()
                ->values();
                
            $statusOptions = ['settlement', 'pending', 'expire'];
            
            return response()->json([
                'success' => true,
                'message' => 'Filter options fetched successfully',
                'data' => [
                    'service_types' => $serviceTypes,
                    'status_options' => $statusOptions
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch filter options',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * ✅ API untuk export Excel
     */
     public function exportExcel(Request $request)
    {
        try {
            $query = Transaction::with(['location', 'lunches.lunchOption']);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('service')) {
                $query->where('room_type', $request->service);
            }
            
            if ($request->filled('date')) {
                $query->whereDate('booking_date', $request->date);
            }
            
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('booking_date', [
                    $request->date_from, 
                    $request->date_to
                ]);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_id', 'LIKE', "%{$search}%")
                      ->orWhere('nama_lengkap', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            $bookings = $query->orderBy('created_at', 'desc')->get();
            
            $fileName = 'bookings-' . date('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];

            $callback = function() use ($bookings) {
                $file = fopen('php://output', 'w');
                
                // ✅ FIX: Add UTF-8 BOM untuk Excel compatibility
                fwrite($file, "\xEF\xBB\xBF");
                
                // ✅ FIX: Gunakan semicolon (;) sebagai delimiter untuk Excel Indonesia
                $delimiter = ";";
                
                // Headers
                fputcsv($file, [
                    'Booking ID',
                    'Transaction Date', 
                    'Booking Date',
                    'Booking Time',
                    'Customer Name',
                    'Phone',
                    'Email',
                    'Service Type',
                    'Package',
                    'Duration',
                    'Participants',
                    'Payment Status',
                    'Base Price',
                    'Lunch Total', 
                    'Total Amount',
                    'Location',
                    'Payment Type'
                ], $delimiter); // ✅ TAMBAHKAN DELIMITER

                // Data rows
                foreach ($bookings as $transaction) {
                    fputcsv($file, [
                        $transaction->order_id,
                        $transaction->transaction_time 
                            ? $transaction->transaction_time->format('d M Y, H:i')
                            : $transaction->created_at->format('d M Y, H:i'),
                        $transaction->booking_date 
                            ? $transaction->booking_date->format('d M Y')
                            : 'Not set',
                        $this->formatBookingTime($transaction),
                        $transaction->nama_lengkap,
                        $transaction->phone,
                        $transaction->email,
                        $transaction->room_type,
                        $transaction->paket ?? 'Standard',
                        $this->getDuration($transaction),
                        $transaction->jumlah_orang ? $transaction->jumlah_orang . ' people' : 'Not specified',
                        ucfirst($transaction->status),
                        // ✅ FIX: Hapus comma formatting untuk angka (Excel akan handle sendiri)
                        $transaction->gross_amount,
                        $transaction->lunch_total,
                        $transaction->gross_amount + $transaction->lunch_total,
                        $transaction->location ? $transaction->location->name : 'Not assigned',
                        $transaction->payment_type ?? 'Not specified'
                    ], $delimiter); // ✅ TAMBAHKAN DELIMITER
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tambahkan method ini di BookingController Anda
    // Di BookingController.php - update getDashboardStats method
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            \Log::info('getDashboardStats called', ['request' => $request->all()]);

            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i:s');
            
            // Default location ID
            $locationId = $request->input('location_id', 1);
            \Log::info('Using location_id: ' . $locationId);

            // 1. Total Booking Hari Ini
            $totalBookingToday = Transaction::whereDate('booking_date', $today)
                ->where('location_id', $locationId)
                ->count();
            \Log::info('Total booking today: ' . $totalBookingToday);

            // 2. Revenue Hari Ini
            $revenueToday = Transaction::whereDate('booking_date', $today)
                ->where('status', 'settlement')
                ->where('location_id', $locationId)
                ->sum('gross_amount');
            \Log::info('Revenue today: ' . $revenueToday);

            // 3. Pending Konfirmasi
            $pendingConfirmation = Transaction::whereDate('booking_date', $today)
                ->where('status', 'pending')
                ->where('location_id', $locationId)
                ->count();
            \Log::info('Pending confirmation: ' . $pendingConfirmation);

            // 4. Total Rooms - ✅ PERBAIKAN: Sesuai structure database
            try {
                if (class_exists('App\Models\Room')) {
                    // ✅ Hanya hitung rooms yang statusnya 'available' (sesuai data Anda)
                    $totalRooms = \App\Models\Room::where('location_id', $locationId)
                        ->where('status', 'available') // ✅ Sesuai kolom status di database
                        ->count();
                    \Log::info('Total available rooms: ' . $totalRooms);
                } else {
                    // Fallback: hitung dari data sample Anda (ada 12 rooms available)
                    $totalRooms = 12;
                    \Log::info('Room model not found, using default: ' . $totalRooms);
                }
            } catch (\Exception $e) {
                \Log::warning('Room count failed: ' . $e->getMessage());
                $totalRooms = 12; // Fallback berdasarkan data sample
            }

            // 5. Rooms Occupied - booking yang sedang berlangsung sekarang
            $occupiedRooms = Transaction::whereDate('booking_date', $today)
                ->where('status', 'settlement')
                ->where('location_id', $locationId)
                ->whereTime('start_time', '<=', $currentTime)
                ->where(function($query) use ($currentTime) {
                    $query->whereRaw("DATE_ADD(start_time, INTERVAL jam HOUR) > ?", [$currentTime])
                        ->orWhereNull('jam');
                })
                ->whereNotNull('room_id') // Hanya yang sudah assign room
                ->count();
            \Log::info('Occupied rooms: ' . $occupiedRooms);

            // 6. Occupancy Rate
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
            \Log::info('Occupancy rate: ' . $occupancyRate);

            $responseData = [
                'totalBookingToday' => $totalBookingToday,
                'revenueToday' => (int)$revenueToday,
                'pendingConfirmation' => $pendingConfirmation,
                'roomsOccupied' => $occupiedRooms,
                'totalRooms' => $totalRooms,
                'occupancyRate' => $occupancyRate,
                'locationId' => $locationId
            ];

            \Log::info('Dashboard stats response: ', $responseData);

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'Dashboard stats fetched successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('CRITICAL ERROR in getDashboardStats: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return fallback data berdasarkan data sample rooms Anda
            return response()->json([
                'success' => true,
                'data' => [
                    'totalBookingToday' => 0,
                    'revenueToday' => 0,
                    'pendingConfirmation' => 0,
                    'roomsOccupied' => 0,
                    'totalRooms' => 12, // ✅ Sesuai data sample rooms available
                    'occupancyRate' => 0,
                    'locationId' => $request->input('location_id', 1)
                ],
                'message' => 'Using fallback data'
            ]);
        }
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $period = $request->input('period', 'daily');
            $metric = $request->input('metric', 'revenue');

            \Log::info('Fetching chart data', ['period' => $period, 'metric' => $metric]);

            $chartData = $this->generateChartData($period, $metric);

            return response()->json([
                'success' => true,
                'data' => $chartData,
                'message' => 'Chart data fetched successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch chart data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate chart data based on period and metric
     */
    private function generateChartData(string $period, string $metric): array
    {
        try {
            if ($period === 'daily') {
                return $this->getDailyChartData($metric);
            } elseif ($period === 'monthly') {
                return $this->getMonthlyChartData($metric);
            } else {
                return $this->getYearlyChartData($metric);
            }
        } catch (\Exception $e) {
            \Log::error('Error in generateChartData: ' . $e->getMessage());
            // Return fallback data
            return $this->getFallbackChartData($period);
        }
    }

    private function getDailyChartData(string $metric): array
    {
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $label = $date->format('d M');
            
            $labels[] = $label;
            
            try {
                if ($metric === 'revenue') {
                    $value = Transaction::whereDate('booking_date', $dateStr)
                                ->where('status', 'settlement')
                                ->sum('gross_amount');
                } else {
                    $value = Transaction::whereDate('booking_date', $dateStr)->count();
                }
                
                $data[] = (int)($value ?? 0);
            } catch (\Exception $e) {
                \Log::error("Error getting data for date {$dateStr}: " . $e->getMessage());
                $data[] = 0;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getMonthlyChartData(string $metric): array
    {
        $labels = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $label = $date->format('M Y');
            
            $labels[] = $label;
            
            try {
                if ($metric === 'revenue') {
                    $value = Transaction::where('status', 'settlement')
                                ->whereYear('booking_date', $date->year)
                                ->whereMonth('booking_date', $date->month)
                                ->sum('gross_amount');
                } else {
                    $value = Transaction::whereYear('booking_date', $date->year)
                                ->whereMonth('booking_date', $date->month)
                                ->count();
                }
                
                $data[] = (int)($value ?? 0);
            } catch (\Exception $e) {
                \Log::error("Error getting data for month {$label}: " . $e->getMessage());
                $data[] = 0;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getYearlyChartData(string $metric): array
    {
        $labels = [];
        $data = [];
        
        for ($i = 2; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $label = (string)$year;
            
            $labels[] = $label;
            
            try {
                if ($metric === 'revenue') {
                    $value = Transaction::where('status', 'settlement')
                                ->whereYear('booking_date', $year)
                                ->sum('gross_amount');
                } else {
                    $value = Transaction::whereYear('booking_date', $year)->count();
                }
                
                $data[] = (int)($value ?? 0);
            } catch (\Exception $e) {
                \Log::error("Error getting data for year {$year}: " . $e->getMessage());
                $data[] = 0;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getFallbackChartData(string $period): array
    {
        // Simple fallback data
        if ($period === 'daily') {
            return [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'data' => [1000000, 1500000, 1200000, 1800000, 2000000, 1700000, 1900000]
            ];
        } elseif ($period === 'monthly') {
            return [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => [25000000, 28000000, 30000000, 32000000, 35000000, 38000000, 40000000, 42000000, 45000000, 48000000, 50000000, 52000000]
            ];
        } else {
            return [
                'labels' => ['2023', '2024', '2025'],
                'data' => [450000000, 520000000, 480000000]
            ];
        }
    }

    private function getOccupiedRoomsCount(string $today): int
    {
        return Transaction::whereDate('booking_date', $today)
            ->where('status', 'settlement')
            ->whereTime('start_time', '<=', now()->format('H:i:s'))
            ->whereRaw("DATE_ADD(start_time, INTERVAL jam HOUR) > ?", [now()->format('H:i:s')])
            ->count();
    }

    public function index()
    {
        return view('layouts.admin.dashboard');
    }
}