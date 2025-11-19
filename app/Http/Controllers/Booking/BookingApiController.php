<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\ServicePrice;
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
    
        private function computeRoomStatus($roomId)
    {
        $now = Carbon::now();
    
        $trx = \App\Models\Transaction::where('room_id', $roomId)
            ->whereIn('status', ['pending', 'settlement'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();
    
        if ($trx->isEmpty()) {
            return 'available';
        }
    
        foreach ($trx as $t) {
    
            if (!$t->booking_date || !$t->start_time) {
                continue;
            }
    
            // ✅ Paksa booking_date jadi format Y-m-d
            try {
                $date = Carbon::parse($t->booking_date)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
    
            // ✅ Paksa start_time jadi format H:i:s
            try {
                $time = Carbon::parse($t->start_time)->format('H:i:s');
            } catch (\Exception $e) {
                continue;
            }
    
            // ✅ Gabungkan dengan benar
            $start = Carbon::parse("$date $time");
    
            // ✅ Copy untuk perhitungan end time
            $end = $start->copy();
    
            // ✅ Pastikan durasi tidak null
            $days   = intval($t->hari ?: 0);
            $weeks  = intval($t->minggu ?: 0);
            $months = intval($t->bulan ?: 0);
            $years  = intval($t->tahun ?: 0);
            $hours  = intval($t->jam ?: 0);
    
            // ✅ Tambahkan durasi
            if ($days > 0)   $end->addDays($days);
            if ($weeks > 0)  $end->addWeeks($weeks);
            if ($months > 0) $end->addMonths($months);
            if ($years > 0)  $end->addYears($years);
            if ($hours > 0)  $end->addHours($hours);
    
            // ✅ Tentukan status
            if ($now->lt($start)) {
                return 'booked';
            }
    
            if ($now->between($start, $end)) {
                return 'occupied';
            }
        }
    
        return 'available';
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
            $locationId = $request->query('location_id');
            $roomTypeName = $request->query('room_type');
            
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
            
            // Cache key based on location and room type
            $cacheKey = "rooms_loc_{$locationId}_type_{$roomType->id}";
            
            $rooms = Cache::remember($cacheKey, 900, function () use ($locationId, $roomType) {
                return Room::select(
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
                    // ✅ UBAH: Ambil room spesifik ATAU multi-purpose
                    ->where(function($query) use ($roomType) {
                        $query->where('room_type_id', $roomType->id)
                            ->orWhereNull('room_type_id');
                    })
                    // ✅ TAMBAH: Pastikan punya service_prices untuk tipe ini
                    ->whereHas('servicePrices', function($query) use ($roomType) {
                        $query->where('room_type_id', $roomType->id);
                    })
                    ->with([
                        'location:id,name,city_id',
                        'location.city:id,name',
                        'roomType:id,name'
                    ])
                    ->orderBy('floor', 'asc')
                    ->orderBy('room_number', 'asc')
                    ->get()
                    ->map(function ($room) use ($roomType) {
                        // ✅ 1. Tentukan display room type
                        $displayRoomType = $room->room_type_id 
                            ? ($room->roomType->name ?? null)
                            : $roomType->name;
                        // ✅ 2. Hitung status ruangan
                        $status = $this->computeRoomStatus($room->id);
                        // ✅ 3. Return data lengkap
                        return [
                            'id' => $room->id,
                            'room_number' => $room->room_number,
                            'capacity' => $room->capacity,
                            'size_m2' => $room->size_m2,
                            'floor' => $room->floor,
                            'status' => $status,           // ✅ aman
                            'room_type' => $displayRoomType,
                            'is_multi_purpose' => is_null($room->room_type_id),
                            'is_selectable' => $status === 'available', // ✅ sekarang tidak error
                            'location' => $room->location->name ?? null,
                            'city' => $room->location->city->name ?? null,
                            'display_name' => "Ruang {$room->room_number} - Lantai {$room->floor} ({$room->capacity} orang, {$room->size_m2}m²)"
                        ];
                    });
            });
            
            Log::info('Rooms fetched successfully', [
                'location_id' => $locationId,
                'room_type' => $roomTypeName,
                'count' => $rooms->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $rooms,
                'count' => $rooms->count()
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
    public function getRoomDetails(int $id): JsonResponse
    {
        try {
            $room = Room::with([
                    'location:id,name,address,city_id',
                    'location.city:id,name',
                    'roomType:id,name'
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
            
            // Build detailed room info
            $roomDetails = [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'capacity' => $room->capacity,
                'size_m2' => $room->size_m2,
                'floor' => $room->floor,
                'room_type' => $room->roomType->name ?? null,
                'room_type_id' => $room->room_type_id,
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
            
            Log::info('Room details fetched successfully', ['room_id' => $id]);
            
            return response()->json([
                'success' => true,
                'data' => $roomDetails
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching room details: ' . $e->getMessage(), [
                'room_id' => $id,
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