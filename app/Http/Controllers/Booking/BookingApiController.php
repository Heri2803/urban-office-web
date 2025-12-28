<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\ServicePrice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class BookingApiController extends Controller
{
    /**
     * Get all active cities
     * 
     * @return JsonResponse
     * 
     * Route: GET /api/cities
     * Example: /api/cities
     */
    public function getCities(): JsonResponse
    {
        try {
            // Cache for 1 hour (cities don't change often)
            $cities = Cache::remember('cities_list', 3600, function () {
                return City::select('id', 'name', 'created_at')
                    ->orderBy('name', 'asc')
                    ->get();
            });
            
            Log::info('Cities fetched successfully', ['count' => $cities->count()]);
            
            return response()->json([
                'success' => true,
                'data' => $cities,
                'count' => $cities->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get locations by city_id
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Route: GET /api/locations?city_id={city_id}
     * Example: /api/locations?city_id=1
     */
    public function getLocations(Request $request): JsonResponse
    {
        try {
            $cityId = $request->query('city_id');
            
            // Validation
            if (!$cityId) {
                return response()->json([
                    'success' => false,
                    'message' => 'City ID is required',
                    'errors' => ['city_id' => ['The city_id field is required']]
                ], 400);
            }

            // Validate city exists
            $cityExists = City::where('id', $cityId)->exists();
            if (!$cityExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found',
                    'errors' => ['city_id' => ['The selected city is invalid']]
                ], 404);
            }
            
            // Cache per city for 30 minutes
            $locations = Cache::remember("locations_city_{$cityId}", 1800, function () use ($cityId) {
                return Location::select('id', 'city_id', 'name', 'address', 'created_at')
                    ->where('city_id', $cityId)
                    ->with('city:id,name')
                    ->orderBy('name', 'asc')
                    ->get();
            });
            
            Log::info('Locations fetched successfully', [
                'city_id' => $cityId,
                'count' => $locations->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $locations,
                'count' => $locations->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching locations: ' . $e->getMessage(), [
                'city_id' => $request->query('city_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch locations',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
    
    private function computeRoomAvailability($roomId, $selectedDate, $selectedStartTime, $durationHours = 1)
{
    $selectedStart = Carbon::parse("$selectedDate $selectedStartTime");
    $selectedEnd = $selectedStart->copy()->addHours($durationHours);

    $transactions = Transaction::where('room_id', $roomId)
        ->whereIn('status', ['pending', 'settlement'])
        ->where('booking_date', $selectedDate)
        ->get();

    $hasConflict = false;

    foreach ($transactions as $transaction) {
        try {
            // ⬇️⬇️⬇️ HANDLE CARBON OBJECT ⬇️⬇️⬇️
            $bookingDate = $transaction->booking_date;
            $startTime = $transaction->start_time;
            
            // Jika booking_date adalah Carbon object, format ke string
            if ($bookingDate instanceof \Carbon\Carbon) {
                $bookingDateString = $bookingDate->format('Y-m-d');
            } else {
                $bookingDateString = $bookingDate;
            }
            
            $tStart = Carbon::parse($bookingDateString . ' ' . $startTime);
            $tEnd = $tStart->copy()->addHours($transaction->jam);
            
            if ($selectedStart->lessThan($tEnd) && $selectedEnd->greaterThan($tStart)) {
                $hasConflict = true;
                break;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error processing transaction in availability check', [
                'transaction_id' => $transaction->id,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'error' => $e->getMessage()
            ]);
            continue;
        }
    }

    return !$hasConflict;
}
    
    
    /**
 * ✅ HITUNG status secara dinamis dari transactions
 * TIDAK ambil dari tabel rooms kecuali tidak ada booking
 */
private function computeRoomStatus($roomId, $selectedDate = null, $selectedTime = null)
{
    \Log::info('🚀 🚀 🚀 COMPUTE ROOM STATUS CALLED 🚀 🚀 🚀', [
        'room_id' => $roomId,
        'selected_date' => $selectedDate,
        'selected_time' => $selectedTime
    ]);
    
    try {
        $now = Carbon::now('Asia/Jakarta');
        $userTime = $selectedDate && $selectedTime 
            ? Carbon::parse($selectedDate . ' ' . $selectedTime, 'Asia/Jakarta')
            : null;

        Log::info('🔍 Computing room status FIXED', [
            'room_id' => $roomId,
            'selected_date' => $selectedDate,
            'selected_time' => $selectedTime,
            'now' => $now->format('Y-m-d H:i:s'),
            'user_time' => $userTime ? $userTime->format('Y-m-d H:i:s') : 'null'
        ]);
        
        // ✅ Ambil SEMUA transaksi aktif
        $transactions = Transaction::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'settlement'])
            ->get();

        Log::info('📊 Transactions found', [
            'room_id' => $roomId,
            'transactions_count' => $transactions->count(),
            'transaction_ids' => $transactions->pluck('id')->toArray()
        ]);
            
        if ($transactions->isEmpty()) {
            $room = Room::find($roomId);
            $status = $room ? $room->status : 'available';
            
            Log::info('✅ No transactions - using room table status', [
                'room_id' => $roomId,
                'status' => $status
            ]);
            
            return $status;
        }
        
        // ✅ PRIORITAS 1: Cek OCCUPIED (booking sedang berjalan SEKARANG)
        Log::info('🔍 Checking PRIORITY 1 - OCCUPIED');
        foreach ($transactions as $transaction) {
            $bookingDate = $transaction->booking_date instanceof Carbon 
                ? $transaction->booking_date->format('Y-m-d') 
                : $transaction->booking_date;
            
            $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, 'Asia/Jakarta');
            $end = $this->calculateEndTime($transaction, 'Asia/Jakarta');
            
            if ($now->between($start, $end)) {
                Log::info('🚨 OCCUPIED detected', [
                    'room_id' => $roomId,
                    'transaction_id' => $transaction->id,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s')
                ]);
                return 'occupied';
            }
        }
        
        // ✅ PRIORITAS 2: Cek BOOKED (untuk tanggal/waktu yang dipilih user)
        if ($userTime) {
            Log::info('🔍 Checking PRIORITY 2 - BOOKED for user time', [
                'user_time' => $userTime->format('Y-m-d H:i:s')
            ]);
            
            foreach ($transactions as $transaction) {
                $bookingDate = $transaction->booking_date instanceof Carbon 
                    ? $transaction->booking_date->format('Y-m-d') 
                    : $transaction->booking_date;
                
                $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, 'Asia/Jakarta');
                $end = $this->calculateEndTime($transaction, 'Asia/Jakarta');
                
                Log::info('   Checking transaction for BOOKED (user time)', [
                    'transaction_id' => $transaction->id,
                    'booking_date' => $bookingDate,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s'),
                    'user_time' => $userTime->format('Y-m-d H:i:s'),
                    'is_between' => $userTime->between($start, $end) ? 'YES' : 'NO'
                ]);
                
                if ($userTime->between($start, $end)) {
                    Log::info('❌ BOOKED - time conflict with user selection', [
                        'room_id' => $roomId,
                        'transaction_id' => $transaction->id,
                        'user_time' => $userTime->format('Y-m-d H:i:s'),
                        'booking_range' => $start->format('H:i') . '-' . $end->format('H:i')
                    ]);
                    return 'booked';
                }
            }
        }
        
        // ✅ PRIORITAS 3: Cek BOOKED (booking aktif pada tanggal yang relevan) - **DIPERBAIKI**
        Log::info('🔍 Checking PRIORITY 3 - Active bookings (FIXED)');
        foreach ($transactions as $transaction) {
            $bookingDate = $transaction->booking_date instanceof Carbon 
                ? $transaction->booking_date->format('Y-m-d') 
                : $transaction->booking_date;
            
            $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, 'Asia/Jakarta');
            $end = $this->calculateEndTime($transaction, 'Asia/Jakarta');
            
            // Jika user mencari untuk tanggal yang sama dengan booking
            if ($userTime && $userTime->isSameDay($start)) {
                Log::info('   Checking same-day booking for user time', [
                    'transaction_id' => $transaction->id,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s'),
                    'user_time' => $userTime->format('Y-m-d H:i:s'),
                    'is_user_time_before_end' => $userTime->lessThan($end) ? 'YES' : 'NO'
                ]);
                
                // **FIX: Hanya return booked jika user_time SEBELUM booking berakhir**
                if ($userTime->lessThan($end)) {
                    Log::info('📅 BOOKED - active booking overlaps with user time', [
                        'room_id' => $roomId,
                        'transaction_id' => $transaction->id,
                        'user_time' => $userTime->format('H:i:s'),
                        'booking_ends' => $end->format('H:i:s')
                    ]);
                    return 'booked';
                } else {
                    Log::info('⏰ Booking ends before user time - AVAILABLE', [
                        'room_id' => $roomId,
                        'transaction_id' => $transaction->id,
                        'booking_ends' => $end->format('H:i:s'),
                        'user_time' => $userTime->format('H:i:s')
                    ]);
                }
            }
            
            // Untuk case tanpa user time (default view) - cek booking hari ini
            if (!$userTime && $start->isToday()) {
                Log::info('   Checking today booking for current time', [
                    'transaction_id' => $transaction->id,
                    'start' => $start->format('Y-m-d H:i:s'),
                    'end' => $end->format('Y-m-d H:i:s'),
                    'now' => $now->format('Y-m-d H:i:s'),
                    'is_now_before_end' => $now->lessThan($end) ? 'YES' : 'NO'
                ]);
                
                if ($now->lessThan($end)) {
                    Log::info('📅 BOOKED - active booking today', [
                        'room_id' => $roomId,
                        'transaction_id' => $transaction->id,
                        'booking_ends' => $end->format('H:i:s'),
                        'now' => $now->format('H:i:s')
                    ]);
                    return 'booked';
                }
            }
        }
        
        // ✅ PRIORITAS 4: AVAILABLE
        Log::info('✅ AVAILABLE - no conflicts', ['room_id' => $roomId]);
        return 'available';
        
    } catch (\Exception $e) {
        Log::error('❌ Error computing room status', [
            'room_id' => $roomId,
            'error' => $e->getMessage()
        ]);
        return 'available';
    }
}

private function calculateEndTime($transaction, $timezone = 'Asia/Jakarta')
{
    $bookingDate = $transaction->booking_date instanceof Carbon 
        ? $transaction->booking_date->format('Y-m-d') 
        : $transaction->booking_date;
        
    $start = Carbon::parse($bookingDate . ' ' . $transaction->start_time, $timezone);
    $end = $start->copy();
    
    // ✅ Perhitungan durasi
    if ($transaction->duration_type === 'yearly' && $transaction->tahun > 0) {
        $end->addYears($transaction->tahun);
    } elseif ($transaction->duration_type === 'monthly' && $transaction->bulan > 0) {
        $end->addMonths($transaction->bulan);
    } elseif ($transaction->duration_type === 'weekly' && $transaction->minggu > 0) {
        $end->addWeeks($transaction->minggu);
    } elseif ($transaction->duration_type === 'daily' && $transaction->hari > 0) {
        $end->addDays($transaction->hari);
    } elseif ($transaction->duration_type === 'hourly' && $transaction->jam > 0) {
        $end->addHours($transaction->jam);
    } else {
        $end->addHour(); // Default 1 jam
    }
    
    return $end;
}

    /**
     * Get available rooms by location_id and room_type
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Route: GET /api/rooms?location_id={location_id}&room_type={room_type}
     * Example: /api/rooms?location_id=1&room_type=Private Office
     */
        public function getRooms(Request $request): JsonResponse
{
    try {
        \Log::info('🔍 [DEBUG] GET ROOMS API CALLED', [
            'all_query_params' => $request->all(),
            'location_id' => $request->query('location_id'),
            'room_type' => $request->query('room_type'), 
            'booking_date' => $request->query('booking_date'),
            'start_time' => $request->query('start_time'),
            'current_server_time' => now()->format('Y-m-d H:i:s')
        ]);

        $locationId = $request->query('location_id');
        $roomTypeName = $request->query('room_type');
        $bookingDate = $request->query('booking_date');
        $startTime = $request->query('start_time');
        
        Log::info('Room API Parameters:', [
            'location_id' => $locationId,
            'room_type' => $roomTypeName,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
        ]);
        
        // Validation
        if (!$locationId || !$roomTypeName) {
            return response()->json([
                'success' => false,
                'message' => 'Location ID and Room Type are required',
                'errors' => [
                    'location_id' => !$locationId ? ['The location_id field is required'] : [],
                    'room_type' => !$roomTypeName ? ['The room_type field is required'] : []
                ]
            ], 400);
        }

        // Validate location exists
        $locationExists = Location::where('id', $locationId)->exists();
        if (!$locationExists) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
                'errors' => ['location_id' => ['The selected location is invalid']]
            ], 404);
        }

        // Get room_type_id from room_types table
        $roomType = RoomType::where('name', $roomTypeName)->first();
        if (!$roomType) {
            return response()->json([
                'success' => false,
                'message' => 'Room type not found',
                'errors' => ['room_type' => ['The selected room type is invalid']]
            ], 404);
        }
        
        // ✅ HAPUS CACHE - langsung query
        $rooms = Room::select(
                'id', 
                'location_id', 
                'room_type_id',
                'room_number', 
                'capacity', 
                'size_m2',
                'status',
                'floor',
                'created_at'
            )
            ->where('location_id', $locationId)
            ->where(function($query) use ($roomType) {
                $query->where('room_type_id', $roomType->id);
                
                $query->orWhere(function($q) use ($roomType) {
                    $q->whereNull('room_type_id')
                      ->whereHas('servicePrices', function($subQuery) use ($roomType) {
                          $subQuery->where('room_type_id', $roomType->id);
                      });
                });
            })
            ->with([
                'location:id,name,city_id',
                'location.city:id,name',
                'roomType:id,name',
                'servicePrices' => function($query) use ($roomType) {
                    $query->where('room_type_id', $roomType->id);
                }
            ])
            ->orderBy('floor', 'asc')
            ->orderBy('room_number', 'asc')
            ->get()
            ->map(function ($room) use ($roomType, $bookingDate, $startTime) {
                $displayRoomType = $room->room_type_id 
                    ? ($room->roomType->name ?? null)
                    : $roomType->name;
                    
                // ✅ Compute status dengan parameter user
                $status = $this->computeRoomStatus($room->id, $bookingDate, $startTime);
                
                $hasServicePrice = $room->servicePrices->isNotEmpty();
                
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'capacity' => $room->capacity,
                    'size_m2' => $room->size_m2,
                    'floor' => $room->floor,
                    'status' => $status,
                    'room_type' => $displayRoomType,
                    'is_multi_purpose' => is_null($room->room_type_id),
                    'is_selectable' => $status === 'available',
                    'location' => $room->location->name ?? null,
                    'city' => $room->location->city->name ?? null,
                    'display_name' => "Ruang {$room->room_number} - Lantai {$room->floor} ({$room->capacity} orang, {$room->size_m2}m²)",
                    'has_service_price' => $hasServicePrice
                ];
            });
        
        Log::info('Rooms fetched successfully', [
            'location_id' => $locationId,
            'room_type' => $roomTypeName,
            'room_type_id' => $roomType->id,
            'count' => $rooms->count()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $rooms,
            'count' => $rooms->count(),
            'filters_applied' => [
                'location_id' => $locationId,
                'room_type_id' => $roomType->id,
                'room_type_name' => $roomTypeName,
                'booking_date' => $bookingDate,
                'start_time' => $startTime
            ]
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error fetching rooms: ' . $e->getMessage(), [
            'location_id' => $request->query('location_id'),
            'room_type' => $request->query('room_type'),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch rooms',
            'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
        ], 500);
    }
}

    /**
     * Get room details by ID (for pricing calculation)
     * 
     * @param int $id
     * @return JsonResponse
     * 
     * Route: GET /api/rooms/{id}
     * Example: /api/rooms/1
     */
        public function getRoomDetails(int $id, Request $request): JsonResponse
    {
        try {
            // ✅ TAMBAH: Ambil room_type dari query parameter
            $roomTypeName = $request->query('room_type');
            
            $room = Room::with([
                    'location:id,name,address,city_id',
                    'location.city:id,name',
                    'roomType:id,name',
                    // ✅ TAMBAH: Load servicePrices dengan filter room_type
                    'servicePrices' => function($query) use ($roomTypeName) {
                        if ($roomTypeName) {
                            $roomType = RoomType::where('name', $roomTypeName)->first();
                            if ($roomType) {
                                $query->where('room_type_id', $roomType->id);
                            }
                        }
                    }
                ])
                ->select(
                    'id', 
                    'location_id', 
                    'room_type_id',
                    'room_number', 
                    'capacity', 
                    'size_m2', 
                    'floor',
                    'created_at'
                )
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found',
                    'errors' => ['room_id' => ['The selected room is invalid']]
                ], 404);
            }
            
            // ✅ VALIDASI: Cek apakah ruangan valid untuk room_type yang dipilih
            $isValidForRoomType = true;
            $validationMessage = null;
            
            if ($roomTypeName) {
                $roomType = RoomType::where('name', $roomTypeName)->first();
                
                if ($roomType) {
                    // Case 1: Ruangan punya room_type_id spesifik - harus match
                    if ($room->room_type_id && $room->room_type_id !== $roomType->id) {
                        $isValidForRoomType = false;
                        $validationMessage = "Ruangan ini khusus untuk {$room->roomType->name}, tidak untuk {$roomTypeName}";
                    }
                    // Case 2: Ruangan multi-purpose - harus punya service_prices untuk room_type ini
                    elseif (is_null($room->room_type_id)) {
                        $hasServicePrice = $room->servicePrices->isNotEmpty();
                        if (!$hasServicePrice) {
                            $isValidForRoomType = false;
                            $validationMessage = "Ruangan multi-purpose ini tidak mendukung layanan {$roomTypeName}";
                        }
                    }
                }
            }
            
            // Build detailed room info
            $roomDetails = [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'size_m2' => $room->size_m2,
                'floor' => $room->floor,
                'room_type' => $room->roomType->name ?? $roomTypeName,
                'room_type_id' => $room->room_type_id,
                'is_multi_purpose' => is_null($room->room_type_id),
                'is_valid_for_selected_type' => $isValidForRoomType,
                'validation_message' => $validationMessage,
                'has_service_price' => $room->servicePrices->isNotEmpty(),
                'location' => [
                    'id' => $room->location->id,
                    'name' => $room->location->name,
                    'address' => $room->location->address,
                    'city_id' => $room->location->city_id,
                    'city_name' => $room->location->city->name ?? null
                ],
                'display_name' => "Ruang {$room->room_number} - Lantai {$room->floor}",
                'full_description' => "Ruang {$room->room_number}, Lantai {$room->floor} - Kapasitas {$room->capacity} orang - Ukuran {$room->size_m2}m²"
            ];
            
            Log::info('Room details fetched successfully', [
                'room_id' => $id,
                'room_type' => $roomTypeName,
                'is_valid' => $isValidForRoomType
            ]);
            
            // ✅ RETURN: Beri error jika ruangan tidak valid untuk tipe yang dipilih
            if (!$isValidForRoomType) {
                return response()->json([
                    'success' => false,
                    'message' => $validationMessage ?? 'Ruangan tidak tersedia untuk tipe yang dipilih',
                    'errors' => ['room_type' => [$validationMessage ?? 'Invalid room type']]
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'data' => $roomDetails
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching room details: ' . $e->getMessage(), [
                'room_id' => $id,
                'room_type' => $request->query('room_type'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    /**
     * Get all room types
     * 
     * @return JsonResponse
     * 
     * Route: GET /api/room-types
     * Example: /api/room-types
     */
    public function getRoomTypes(): JsonResponse
    {
        try {
            // Cache for 1 hour
            $roomTypes = Cache::remember('room_types_list', 3600, function () {
                return RoomType::select('id', 'name', 'created_at')
                    ->orderBy('name', 'asc')
                    ->get();
            });
            
            Log::info('Room types fetched successfully', ['count' => $roomTypes->count()]);
            
            return response()->json([
                'success' => true,
                'data' => $roomTypes,
                'count' => $roomTypes->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching room types: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room types',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Clear cache for specific resources
     * (Admin only - add auth middleware)
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Route: POST /api/cache/clear
     */
    public function clearCache(Request $request): JsonResponse
    {
        try {
            $type = $request->input('type', 'all');
            
            switch ($type) {
                case 'cities':
                    Cache::forget('cities_list');
                    break;
                    
                case 'locations':
                    // Clear all location caches
                    $cities = City::pluck('id');
                    foreach ($cities as $cityId) {
                        Cache::forget("locations_city_{$cityId}");
                    }
                    break;
                    
                case 'rooms':
                    // Clear all room caches
                    Cache::flush(); // Or implement more specific cache keys
                    break;
                    
                case 'room_types':
                    Cache::forget('room_types_list');
                    break;
                    
                case 'all':
                default:
                    Cache::flush();
                    break;
            }
            
            Log::info('Cache cleared successfully', ['type' => $type]);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'type' => $type
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
     // ✅ Ambil data service_prices berdasarkan room_id
    public function getServicePriceByRoom($roomId)
    {
        $servicePrice = ServicePrice::with(['room', 'roomType'])
            ->where('room_id', $roomId)
            ->first();

        if (!$servicePrice) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json([
            'id' => $servicePrice->id,
            'room_id' => $servicePrice->room_id,
            'room_type_id' => $servicePrice->room_type_id,
            'room_type' => $servicePrice->roomType->name ?? null,
            'duration_type' => $servicePrice->duration_type,
            'base_price' => $servicePrice->base_price,
            'coffee_break_option' => $servicePrice->coffee_break_option,
            'coffee_break_price' => $servicePrice->coffee_break_price,
            'deposit' => $servicePrice->deposit,
            'created_at' => $servicePrice->created_at,
            'updated_at' => $servicePrice->updated_at,
        ]);
    }
    

}