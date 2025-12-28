<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExistingCustomerBookingController extends Controller
{
    public function index()
    {
        // Gunakan file yang sama, tapi dengan parameter type
        return view('layouts.admin.walk-in-booking', [
            'booking_type' => 'existing_customer'  // Parameter untuk bedakan
        ]);
    }
    /**
     * Search existing customers (users with role='customer')
     */
    public function searchCustomers(Request $request)
{
    $request->validate([
        'q' => 'required|string|min:2'
    ]);

    $query = $request->q;
    
    try {
        // Cari user dengan role customer ATAU mitra yang punya transaksi
        $users = User::whereIn('role', ['customer', 'mitra' , 'admin']) // <-- Tambahkan mitra
            ->where(function($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%{$query}%")
                    ->orWhere('telephone', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereHas('transactions', function($q) {
                $q->whereIn('status', ['settlement', 'success', 'completed']);
            })
            ->withCount(['transactions' => function($query) {
                $query->whereIn('status', ['settlement', 'success', 'completed']);
            }])
            ->with(['transactions' => function($q) {
                $q->whereIn('status', ['settlement', 'success', 'completed'])
                  ->latest()
                  ->limit(1);
            }])
            ->limit(10)
            ->get();
        
        \Log::info('Search results:', [
            'query' => $query,
            'users_found' => $users->count(),
            'roles_found' => $users->pluck('role')->unique()->toArray()
        ]);
        
        $customers = $users->map(function($user) {
            $lastTransaction = $user->transactions->first();
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->telephone,
                'email' => $user->email,
                'role' => $user->role, // Tampilkan role di frontend
                'booking_count' => $user->transactions_count,
                'last_booking' => $lastTransaction ? [
                    'service' => $lastTransaction->room_type,
                    'date' => $lastTransaction->booking_date,
                    'status' => $lastTransaction->status,
                    'created_at' => $lastTransaction->created_at->format('Y-m-d')
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'search_query' => $query,
            'debug' => [
                'total_found' => $customers->count(),
                'query' => $query,
                'roles_searched' => ['customer', 'mitra']
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Search error:', [
            'message' => $e->getMessage(),
            'query' => $query,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Search failed: ' . $e->getMessage(),
            'customers' => []
        ], 500);
    }
}

    /**
     * Get customer's booking history
     */
    public function getCustomerHistory($userId)
    {
        $user = User::find($userId);
        
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $transactions = Transaction::where('user_id', $userId)
            ->whereIn('status', ['settlement', 'success', 'completed', 'pending'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'order_id' => $transaction->order_id,
                    'service_type' => $transaction->room_type,
                    'room_id' => $transaction->room_id,
                    'package' => $transaction->paket,
                    'participants' => $transaction->jumlah_orang,
                    'amount' => $transaction->gross_amount,
                    'deposit' => $transaction->deposit,
                    'date' => $transaction->booking_date,
                    'start_time' => $transaction->start_time,
                    'status' => $transaction->status,
                    'payment_type' => $transaction->payment_type,
                    'created_at' => $transaction->created_at
                ];
            });

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->telephone
            ],
            'history' => $transactions,
            'total_bookings' => $transactions->count()
        ]);
    }

    /**
     * Calculate loyalty discount for customer
     */
    public function calculateLoyaltyDiscount($userId)
    {
        $user = User::find($userId);
        
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $bookingCount = Transaction::where('user_id', $userId)
            ->whereIn('status', ['settlement', 'success', 'completed'])
            ->count();

        $discount = 0;
        $discountTier = 'No tier';
        
        if ($bookingCount >= 10) {
            $discount = 15;
            $discountTier = 'Gold (10+ bookings)';
        } elseif ($bookingCount >= 5) {
            $discount = 10;
            $discountTier = 'Silver (5-9 bookings)';
        } elseif ($bookingCount >= 3) {
            $discount = 5;
            $discountTier = 'Bronze (3-4 bookings)';
        }

        return response()->json([
            'success' => true,
            'customer_id' => $userId,
            'customer_name' => $user->name,
            'booking_count' => $bookingCount,
            'discount_percentage' => $discount,
            'discount_tier' => $discountTier,
            'message' => $discount > 0 
                ? "Loyalty discount {$discount}% applied ({$discountTier})" 
                : "No loyalty discount available (Minimum 3 bookings required)"
        ]);
    }

    /**
     * Create booking for existing customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_type' => 'required|string', // Pakai room_type bukan service_type
            'booking_date' => 'required|date',
            'paket' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'jumlah_orang' => 'nullable|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'gross_amount' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string|in:cash,transfer,card',
            'status' => 'nullable|string|in:pending,settlement,success,cancelled'
        ]);

        // Verify user is a customer
        $user = User::find($validated['user_id']);
        
        if ($user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Only customers can make bookings'
            ], 422);
        }

        // Check if customer has previous bookings (optional validation)
        $hasPreviousBookings = Transaction::where('user_id', $validated['user_id'])
            ->whereIn('status', ['settlement', 'success', 'completed'])
            ->exists();

        if (!$hasPreviousBookings) {
            return response()->json([
                'success' => false,
                'message' => 'Customer must have at least one previous completed booking'
            ], 422);
        }

        // Generate order ID
        $orderId = 'EXB-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Create transaction dengan field yang sesuai tabel
        $transaction = Transaction::create([
            'user_id' => $validated['user_id'],
            'room_type' => $validated['room_type'],
            'booking_date' => $validated['booking_date'],
            'paket' => $validated['paket'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'jumlah_orang' => $validated['jumlah_orang'] ?? null,
            'room_id' => $validated['room_id'] ?? null,
            'nama_lengkap' => $user->name,
            'email' => $user->email,
            'phone' => $user->telephone,
            'order_id' => $orderId,
            'gross_amount' => $validated['gross_amount'],
            'deposit' => $validated['deposit'] ?? 0,
            'payment_type' => $validated['payment_type'] ?? 'cash',
            'status' => $validated['status'] ?? 'pending',
            // Tambahan field untuk existing customer booking
            'hari' => date('l', strtotime($validated['booking_date'])),
            'minggu' => date('W', strtotime($validated['booking_date'])),
            'bulan' => date('m', strtotime($validated['booking_date'])),
            'tahun' => date('Y', strtotime($validated['booking_date'])),
            'jam' => $validated['start_time'] ? date('H', strtotime($validated['start_time'])) : null,
            'is_read' => 0,
            'transaction_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $orderId,
            'transaction_id' => $transaction->id,
            'message' => 'Booking created successfully for existing customer',
            'discount_applied' => $request->has('discount_percentage') ? $request->discount_percentage : 0
        ]);
    }

    /**
     * Get available room types (untuk dropdown)
     */
    public function getRoomTypes()
    {
        $roomTypes = Transaction::select('room_type')
            ->whereNotNull('room_type')
            ->distinct()
            ->pluck('room_type');

        return response()->json([
            'success' => true,
            'room_types' => $roomTypes
        ]);
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStats($userId)
    {
        $user = User::find($userId);
        
        if (!$user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        $totalSpent = Transaction::where('user_id', $userId)
            ->whereIn('status', ['settlement', 'success', 'completed'])
            ->sum('gross_amount');

        $avgAmount = Transaction::where('user_id', $userId)
            ->whereIn('status', ['settlement', 'success', 'completed'])
            ->avg('gross_amount');

        $favoriteRoomType = Transaction::where('user_id', $userId)
            ->select('room_type', \DB::raw('COUNT(*) as count'))
            ->groupBy('room_type')
            ->orderBy('count', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'customer_id' => $userId,
            'total_bookings' => Transaction::where('user_id', $userId)->count(),
            'completed_bookings' => Transaction::where('user_id', $userId)
                ->whereIn('status', ['settlement', 'success', 'completed'])
                ->count(),
            'total_spent' => $totalSpent,
            'average_booking_amount' => $avgAmount,
            'favorite_room_type' => $favoriteRoomType ? $favoriteRoomType->room_type : null,
            'booking_frequency' => $this->calculateBookingFrequency($userId)
        ]);
    }

    /**
     * Helper: Calculate booking frequency
     */
    private function calculateBookingFrequency($userId)
    {
        $firstBooking = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->first();
        
        $lastBooking = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$firstBooking || !$lastBooking) {
            return 'No data';
        }

        $totalBookings = Transaction::where('user_id', $userId)->count();
        $daysBetween = $firstBooking->created_at->diffInDays($lastBooking->created_at);
        
        if ($daysBetween == 0) return 'First time booking';
        
        $frequency = $totalBookings / $daysBetween;
        
        if ($frequency > 0.1) return 'Frequent (almost daily)';
        if ($frequency > 0.03) return 'Regular (weekly)';
        if ($frequency > 0.014) return 'Occasional (bi-weekly)';
        
        return 'Infrequent (monthly or less)';
    }

    /**
     * Get available services for booking
     */
    public function getAvailableServices()
    {
        try {
            // Ambil unique room types dari service_prices atau transactions
            $roomTypes = \DB::table('service_prices')
                ->select('room_type_id')
                ->distinct()
                ->get()
                ->pluck('room_type_id');
            
            // Mapping room_type_id ke nama service
            $roomTypeNames = [
                1 => ['id' => 'private_office', 'name' => 'Private Office'],
                2 => ['id' => 'meeting', 'name' => 'Meeting Room'],
                3 => ['id' => 'event', 'name' => 'Event Space'],
                4 => ['id' => 'coworking', 'name' => 'Coworking Space'],
                5 => ['id' => 'virtual_office', 'name' => 'Virtual Office'],
                6 => ['id' => 'sharing_room', 'name' => 'Sharing Room']
            ];
            
            $services = [];
            foreach ($roomTypes as $typeId) {
                if (isset($roomTypeNames[$typeId])) {
                    $service = $roomTypeNames[$typeId];
                    
                    // Get price range untuk service ini
                    $priceInfo = \DB::table('service_prices')
                        ->where('room_type_id', $typeId)
                        ->selectRaw('MIN(base_price) as min_price, MAX(base_price) as max_price')
                        ->first();
                    
                    $services[] = [
                        'id' => $service['id'],
                        'name' => $service['name'],
                        'description' => $this->getServiceDescription($service['id']),
                        'type' => $service['id'],
                        'room_type_id' => $typeId,
                        'min_price' => $priceInfo->min_price ?? 0,
                        'max_price' => $priceInfo->max_price ?? 0,
                        'icon' => $this->getServiceIcon($service['id'])
                    ];
                }
            }
            
            // Fallback jika tidak ada data di service_prices
            if (empty($services)) {
                $services = $this->getDefaultServices();
            }
            
            return response()->json([
                'success' => true,
                'services' => $services
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting available services:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load services',
                'services' => $this->getDefaultServices()
            ]);
        }
    }

    private function getServiceDescription($serviceId)
    {
        $descriptions = [
            'meeting' => 'Professional meeting space with facilities',
            'private_office' => 'Dedicated private workspace',
            'event' => 'Large venue for events and gatherings',
            'coworking' => 'Flexible shared workspace',
            'virtual_office' => 'Business address and mail handling service',
            'sharing_room' => 'Shared office space with amenities'
        ];
        
        return $descriptions[$serviceId] ?? 'Professional workspace';
    }

    private function getServiceIcon($serviceId)
    {
        $icons = [
            'meeting' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'private_office' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'event' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            'coworking' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
        ];
        
        return $icons[$serviceId] ?? 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    /**
     * Get packages for specific service type
     */
    public function getPackages($serviceType)
    {
        try {
            // Map service type to room_type_id
            $roomTypeMap = [
                'meeting' => 2,
                'private_office' => 1,
                'event' => 3,
                'coworking' => 4,
                'virtual_office' => 5,
                'sharing_room' => 6
            ];
            
            $roomTypeId = $roomTypeMap[$serviceType] ?? null;
            
            if (!$roomTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid service type'
                ], 400);
            }
            
            // Ambil packages dari service_prices
            $packages = \DB::table('service_prices')
                ->where('room_type_id', $roomTypeId)
                ->select(
                    'id',
                    'duration_type',
                    'duration',
                    'base_price',
                    'coffee_break_option',
                    'coffee_break_price',
                    'deposit'
                )
                ->orderBy('duration')
                ->get()
                ->map(function ($item) use ($serviceType) {
                    return [
                        'id' => $item->id,
                        'name' => $this->formatPackageName($item->duration_type, $item->duration, $serviceType),
                        'duration_type' => $item->duration_type,
                        'duration' => $item->duration,
                        'price' => (float) $item->base_price,
                        'base_price' => (float) $item->base_price,
                        'coffee_break_price' => $item->coffee_break_price ? (float) $item->coffee_break_price : null,
                        'deposit' => $item->deposit ? (float) $item->deposit : 0,
                        'has_coffee_break' => !empty($item->coffee_break_option)
                    ];
                });
            
            return response()->json([
                'success' => true,
                'service_type' => $serviceType,
                'room_type_id' => $roomTypeId,
                'packages' => $packages
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting packages:', [
                'service_type' => $serviceType,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load packages',
                'packages' => $this->getDefaultPackages($serviceType)
            ]);
        }
    }

    private function formatPackageName($durationType, $duration, $serviceType)
    {
        $durations = [
            'hour' => 'jam',
            'day' => 'hari',
            'week' => 'minggu',
            'month' => 'bulan',
            'year' => 'tahun'
        ];
        
        $type = $durations[$durationType] ?? $durationType;
        
        if ($serviceType === 'meeting') {
            if ($duration == 1) return '1 Jam';
            if ($duration == 2) return '2 Jam';
            if ($duration == 4) return '4 Jam';
            if ($duration == 8) return 'Full Day (8 Jam)';
        }
        
        return "{$duration} " . ucfirst($type);
    }

    /**
     * Get available time slots for booking
     */
    public function getAvailableTimeSlots(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'date' => 'required|date',
            'duration' => 'required|integer|min:1'
        ]);
        
        try {
            $serviceType = $request->service_type;
            $date = $request->date;
            $duration = $request->duration;
            
            // Map service type to room_type_id
            $roomTypeMap = [
                'meeting' => 2,
                'private_office' => 1,
                'event' => 3,
                'coworking' => 4,
                'virtual_office' => 5,
                'sharing_room' => 6
            ];
            
            $roomTypeId = $roomTypeMap[$serviceType] ?? null;
            
            if (!$roomTypeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid service type'
                ], 400);
            }
            
            // Generate all possible time slots (08:00 - 20:00)
            $allSlots = [];
            for ($hour = 8; $hour < 20; $hour++) {
                $allSlots[] = sprintf('%02d:00', $hour);
                $allSlots[] = sprintf('%02d:30', $hour);
            }
            
            // Cari booking yang sudah ada di tanggal tersebut untuk service type ini
            $existingBookings = Transaction::where('booking_date', $date)
                ->where(function($query) use ($roomTypeId, $serviceType) {
                    // Cari berdasarkan room_type_id jika ada, atau berdasarkan room_type string
                    if ($roomTypeId) {
                        $query->whereHas('room', function($q) use ($roomTypeId) {
                            $q->where('room_type_id', $roomTypeId);
                        });
                    } else {
                        $query->where('room_type', 'like', "%{$serviceType}%");
                    }
                })
                ->whereIn('status', ['pending', 'settlement', 'success'])
                ->get(['start_time', 'jam', 'hari', 'minggu', 'bulan', 'tahun', 'paket']);
            
            // Filter slots yang tersedia
            $availableSlots = [];
            
            foreach ($allSlots as $slot) {
                $isAvailable = true;
                
                // Cek apakah slot ini bertabrakan dengan booking yang ada
                foreach ($existingBookings as $booking) {
                    if ($this->isTimeSlotConflict($slot, $duration, $booking)) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                if ($isAvailable) {
                    $availableSlots[] = $slot;
                }
            }
            
            return response()->json([
                'success' => true,
                'service_type' => $serviceType,
                'date' => $date,
                'duration' => $duration,
                'available_times' => $availableSlots,
                'debug' => [
                    'total_slots' => count($allSlots),
                    'available_slots' => count($availableSlots),
                    'existing_bookings' => $existingBookings->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting time slots:', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            // Return default time slots
            $defaultSlots = [];
            for ($hour = 8; $hour < 20; $hour++) {
                $defaultSlots[] = sprintf('%02d:00', $hour);
                if ($hour < 20) {
                    $defaultSlots[] = sprintf('%02d:30', $hour);
                }
            }
            
            return response()->json([
                'success' => true,
                'available_times' => $defaultSlots,
                'message' => 'Using default time slots'
            ]);
        }
    }

    /**
     * Check if time slots conflict
     */
    private function isTimeSlotConflict($newStart, $newDurationHours, $existingBooking)
    {
        // Parse new start time
        list($newHour, $newMinute) = explode(':', $newStart);
        $newStartMinutes = (int)$newHour * 60 + (int)$newMinute;
        $newEndMinutes = $newStartMinutes + ($newDurationHours * 60);
        
        // Parse existing booking start time
        list($existHour, $existMinute) = explode(':', $existingBooking->start_time);
        $existStartMinutes = (int)$existHour * 60 + (int)$existMinute;
        
        // Calculate existing booking end time based on paket
        $existDurationHours = $this->getDurationFromPaket($existingBooking->paket, $existingBooking);
        $existEndMinutes = $existStartMinutes + ($existDurationHours * 60);
        
        // Check for overlap
        return !($newEndMinutes <= $existStartMinutes || $newStartMinutes >= $existEndMinutes);
    }

    /**
     * Get duration in hours from paket field
     */
    private function getDurationFromPaket($paket, $booking)
    {
        // Default duration (in hours)
        $defaultDuration = 2;
        
        if (!$paket) {
            return $defaultDuration;
        }
        
        // Parse paket to get duration
        if (strpos($paket, 'h') !== false || strpos($paket, 'hour') !== false) {
            // Hourly: '1h', '2h', '4h', '8h', 'hourly'
            if ($paket === 'hourly') {
                return $booking->jam ?? $defaultDuration;
            }
            
            $hours = (int) $paket;
            return $hours > 0 ? $hours : $defaultDuration;
        }
        
        // For daily/weekly/monthly bookings, assume full day (8 hours)
        if (in_array($paket, ['daily', 'weekly', 'monthly', 'yearly'])) {
            return 8; // Full day
        }
        
        return $defaultDuration;
    }

    /**
     * Get available facilities
     */
    public function getAvailableFacilities()
    {
        // Bisa diambil dari database jika ada tabel facilities
        // Untuk sekarang, hardcode dulu
        $facilities = [
            'Projector',
            'Whiteboard',
            'Sound System',
            'Video Conference',
            'Coffee/Tea',
            'WiFi',
            'Printing',
            'Flipchart',
            'Microphone'
        ];
        
        return response()->json([
            'success' => true,
            'facilities' => $facilities
        ]);
    }

    /**
     * Get service price details (similar to customer side)
     */
    public function getServicePrice(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|integer',
            'room_id' => 'nullable|integer',
            'duration' => 'required|integer|min:1',
            'duration_type' => 'required|in:hour,day,week,month,year',
            'people' => 'nullable|integer|min:1',
            'service_category_id' => 'nullable|integer',
            'coffee_break_option' => 'nullable|integer|in:0,1,2'
        ]);
        
        try {
            $roomTypeId = $request->room_type_id;
            $roomId = $request->room_id;
            $duration = $request->duration;
            $durationType = $request->duration_type;
            $people = $request->people ?: 1;
            $serviceCategoryId = $request->service_category_id;
            $coffeeBreakOption = $request->coffee_break_option ?: 0;
            
            // Cari harga di service_prices
            $query = \DB::table('service_prices')
                ->where('room_type_id', $roomTypeId)
                ->where('duration_type', $durationType)
                ->where('duration', $duration);
            
            // Filter by room_id jika ada
            if ($roomId) {
                $query->where('room_id', $roomId);
            }
            
            // Filter by service_category_id jika ada (untuk meeting: 1=small, 2=big)
            if ($serviceCategoryId) {
                $query->where('service_category_id', $serviceCategoryId);
            }
            
            // Filter by coffee_break_option jika ada
            if ($coffeeBreakOption > 0) {
                $query->where('coffee_break_option', $coffeeBreakOption);
            }
            
            $priceData = $query->first();
            
            if (!$priceData) {
                // Fallback: cari harga terdekat
                $fallbackPrice = \DB::table('service_prices')
                    ->where('room_type_id', $roomTypeId)
                    ->where('duration_type', $durationType)
                    ->orderBy('duration')
                    ->first();
                
                if (!$fallbackPrice) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Price not found for this service'
                    ], 404);
                }
                
                $priceData = $fallbackPrice;
            }
            
            // Calculate total price
            $basePrice = (float) $priceData->base_price;
            $coffeeBreakPrice = $priceData->coffee_break_price ? (float) $priceData->coffee_break_price : null;
            $deposit = $priceData->deposit ? (float) $priceData->deposit : 0;
            
            // Untuk meeting room, kalikan dengan jumlah orang jika big meeting
            $totalPrice = $basePrice;
            if ($roomTypeId == 2 && $serviceCategoryId == 2) { // Big meeting
                $totalPrice = $basePrice * $people;
            }
            
            // Apply coffee break price jika ada
            if ($coffeeBreakOption > 0 && $coffeeBreakPrice) {
                if ($roomTypeId == 2 && $serviceCategoryId == 2) { // Big meeting with coffee break
                    $totalPrice += ($coffeeBreakPrice * $people);
                } else {
                    $totalPrice = $coffeeBreakPrice;
                }
            }
            
            return response()->json([
                'success' => true,
                'price_data' => $priceData,
                'calculated' => [
                    'base_price' => $basePrice,
                    'coffee_break_price' => $coffeeBreakPrice,
                    'total_price' => $totalPrice,
                    'deposit' => $deposit,
                    'people' => $people,
                    'duration_hours' => $this->convertDurationToHours($duration, $durationType)
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting service price:', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get price: ' . $e->getMessage()
            ], 500);
        }
    }

    private function convertDurationToHours($duration, $durationType)
    {
        switch ($durationType) {
            case 'hour': return $duration;
            case 'day': return $duration * 8; // 8 hours per day
            case 'week': return $duration * 40; // 40 hours per week
            case 'month': return $duration * 160; // 160 hours per month
            case 'year': return $duration * 1920; // 1920 hours per year
            default: return $duration;
        }
    }

    /**
 * Get available rooms for booking based on criteria
 */
public function getAvailableRooms(Request $request)
{
    try {
        $request->validate([
            'room_type' => 'required|string', // 'Meeting Room', 'Private Office', dll
            'booking_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'duration_hours' => 'nullable|integer|min:1',
            'duration_type' => 'nullable|in:hour,day,week,month,year',
            'location_id' => 'nullable|exists:locations,id'
        ]);
        
        $roomType = $request->room_type;
        $bookingDate = $request->booking_date;
        $startTime = $request->start_time;
        $durationHours = $request->duration_hours ?: 2; // Default 2 jam
        $durationType = $request->duration_type ?: 'hour';
        $locationId = $request->location_id;
        
        // Map room_type string ke room_type_id jika perlu
        $roomTypeId = $this->mapRoomTypeToId($roomType);
        
        // Query rooms berdasarkan type
        $query = Room::with(['roomType', 'location']);
        
        if ($roomTypeId) {
            $query->where('room_type_id', $roomTypeId);
        } else {
            // Jika room_type_id tidak ada, cari berdasarkan nama
            $query->whereHas('roomType', function($q) use ($roomType) {
                $q->where('name', 'like', "%{$roomType}%");
            });
        }
        
        // Filter by location jika ada
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        $rooms = $query->get();
        
        // Filter rooms yang available untuk tanggal & waktu tersebut
        $availableRooms = $rooms->filter(function($room) use ($bookingDate, $startTime, $durationHours) {
            return $this->isRoomAvailable($room, $bookingDate, $startTime, $durationHours);
        });
        
        // Format response
        $formattedRooms = $availableRooms->map(function($room) use ($bookingDate) {
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'room_name' => $room->room_name,
                'room_type_id' => $room->room_type_id,
                'room_type_name' => $room->roomType ? $room->roomType->name : null,
                'capacity' => $room->capacity,
                'size_m2' => $room->size_m2,
                'floor' => $room->floor,
                'location_id' => $room->location_id,
                'location_name' => $room->location ? $room->location->name : null,
                'facilities' => $room->facilities ? json_decode($room->facilities, true) : [],
                'images' => $room->images ? json_decode($room->images, true) : [],
                'status' => 'available',
                'next_booking' => $this->getNextBooking($room, $bookingDate),
                'price_info' => $this->getPriceInfo($room) // Ambil harga dari service_prices
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedRooms,
            'meta' => [
                'total_rooms' => $rooms->count(),
                'available_rooms' => $availableRooms->count(),
                'criteria' => [
                    'room_type' => $roomType,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'duration_hours' => $durationHours,
                    'duration_type' => $durationType,
                    'location_id' => $locationId
                ]
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting available rooms:', [
            'request' => $request->all(),
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get available rooms: ' . $e->getMessage(),
            'data' => []
        ], 500);
    }
}

/**
 * Check if room is available for specific date/time
 */
private function isRoomAvailable($room, $bookingDate, $startTime, $durationHours)
{
    // Jika tidak ada start_time, hanya check berdasarkan date
    if (!$startTime) {
        return $this->isRoomAvailableForDate($room, $bookingDate);
    }
    
    // Hitung end time
    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $startTime);
    $endDateTime = clone $startDateTime;
    $endDateTime->addHours($durationHours);
    
    // Cari booking yang overlap
    $conflictingBookings = Transaction::where('room_id', $room->id)
        ->whereIn('status', ['pending', 'settlement', 'success']) // Booking yang aktif
        ->where('booking_date', $bookingDate)
        ->where(function($query) use ($startDateTime, $endDateTime) {
            // Case 1: Booking existing dimulai selama slot baru
            $query->where(function($q) use ($startDateTime, $endDateTime) {
                $q->where('start_time', '>=', $startDateTime->format('H:i:s'))
                  ->where('start_time', '<', $endDateTime->format('H:i:s'));
            })
            ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                // Case 2: Booking existing berakhir selama slot baru
                // Perlu hitung end_time dari existing booking
                $q->whereRaw("DATE_ADD(CONCAT(booking_date, ' ', start_time), 
                               INTERVAL 
                               CASE 
                                 WHEN paket = 'hourly' THEN jam * 60
                                 WHEN paket = 'daily' THEN hari * 24 * 60
                                 WHEN paket = 'weekly' THEN minggu * 7 * 24 * 60
                                 WHEN paket = 'monthly' THEN bulan * 30 * 24 * 60
                                 WHEN paket = 'yearly' THEN tahun * 365 * 24 * 60
                                 ELSE 120 -- default 2 jam
                               END MINUTE) 
                               > ?", [$startDateTime->format('Y-m-d H:i:s')])
                  ->where('start_time', '<', $endDateTime->format('H:i:s'));
            });
        })
        ->exists();
    
    return !$conflictingBookings;
}

/**
 * Check room availability for entire date (tanpa specific time)
 */
private function isRoomAvailableForDate($room, $bookingDate)
{
    // Cek apakah ada booking aktif di tanggal tersebut
    $bookingsOnDate = Transaction::where('room_id', $room->id)
        ->whereIn('status', ['pending', 'settlement', 'success'])
        ->where('booking_date', $bookingDate)
        ->count();
    
    // Jika ada booking, mungkin masih ada slot yang available
    // Tapi untuk simplicity, kita anggap tidak available jika ada booking
    return $bookingsOnDate === 0;
}

/**
 * Get next booking for the room
 */
private function getNextBooking($room, $fromDate)
{
    $nextBooking = Transaction::where('room_id', $room->id)
        ->whereIn('status', ['pending', 'settlement', 'success'])
        ->where('booking_date', '>=', $fromDate)
        ->orderBy('booking_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->first();
    
    if (!$nextBooking) {
        return null;
    }
    
    return [
        'date' => $nextBooking->booking_date,
        'start_time' => $nextBooking->start_time,
        'end_time' => $this->calculateEndTime($nextBooking),
        'duration' => $this->getBookingDuration($nextBooking)
    ];
}

/**
 * Calculate end time from booking
 */
private function calculateEndTime($booking)
{
    $start = Carbon::createFromFormat('Y-m-d H:i', $booking->booking_date . ' ' . $booking->start_time);
    
    $durationHours = $this->getBookingDurationInHours($booking);
    
    return $start->addHours($durationHours)->format('H:i');
}

/**
 * Get booking duration in hours
 */
private function getBookingDurationInHours($booking)
{
    if ($booking->paket === 'hourly') {
        return $booking->jam ?: 2;
    }
    
    if ($booking->paket === 'daily') {
        return ($booking->hari ?: 1) * 8; // 8 jam per hari
    }
    
    if ($booking->paket === 'weekly') {
        return ($booking->minggu ?: 1) * 40; // 40 jam per minggu
    }
    
    if ($booking->paket === 'monthly') {
        return ($booking->bulan ?: 1) * 160; // 160 jam per bulan
    }
    
    if ($booking->paket === 'yearly') {
        return ($booking->tahun ?: 1) * 1920; // 1920 jam per tahun
    }
    
    return 2; // Default 2 jam
}

/**
 * Get readable duration string
 */
private function getBookingDuration($booking)
{
    if ($booking->paket === 'hourly' && $booking->jam) {
        return $booking->jam . ' jam';
    }
    
    if ($booking->paket === 'daily' && $booking->hari) {
        return $booking->hari . ' hari';
    }
    
    if ($booking->paket === 'weekly' && $booking->minggu) {
        return $booking->minggu . ' minggu';
    }
    
    if ($booking->paket === 'monthly' && $booking->bulan) {
        return $booking->bulan . ' bulan';
    }
    
    if ($booking->paket === 'yearly' && $booking->tahun) {
        return $booking->tahun . ' tahun';
    }
    
    return '2 jam'; // Default
}

/**
 * Get price info for room
 */
private function getPriceInfo($room)
{
    // Ambil harga dari service_prices untuk room ini
    $prices = \DB::table('service_prices')
        ->where('room_id', $room->id)
        ->orWhere(function($query) use ($room) {
            // Jika tidak ada harga spesifik untuk room, ambil berdasarkan room_type
            if ($room->room_type_id) {
                $query->where('room_type_id', $room->room_type_id)
                      ->whereNull('room_id');
            }
        })
        ->orderBy('base_price', 'asc')
        ->get()
        ->map(function($price) {
            return [
                'duration_type' => $price->duration_type,
                'duration' => $price->duration,
                'base_price' => (float) $price->base_price,
                'coffee_break_price' => $price->coffee_break_price ? (float) $price->coffee_break_price : null,
                'deposit' => $price->deposit ? (float) $price->deposit : 0
            ];
        });
    
    return $prices->first(); // Return harga terendah
}

/**
 * Map room type string to ID
 */
private function mapRoomTypeToId($roomType)
{
    $mapping = [
        'Meeting Room' => 2,
        'Private Office' => 1,
        'Event Space' => 3,
        'Coworking Space' => 4,
        'Virtual Office' => 5,
        'Sharing Room' => 6
    ];
    
    return $mapping[$roomType] ?? null;
}
}