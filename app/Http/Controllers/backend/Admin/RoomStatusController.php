<?php

namespace App\Http\Controllers\backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Room;

class RoomStatusController extends Controller
{
    /**
     * Get ALL rooms dari tabel rooms (bukan transactions)
     */
    public function getUniqueRooms()
    {
        try {
            Log::info('🔄 Fetching ALL rooms from rooms table');

            $allRooms = DB::table('rooms')
                ->select(
                    'id',
                    'room_number as number',
                    'room_type_id', 
                    'floor',
                    'capacity',
                    'size_m2',
                    'status as base_status',
                    'location_id'
                )
                ->orderBy('room_number')
                ->get();

            Log::info("✅ Found {$allRooms->count()} rooms in database");

            // Debug: Log semua room_type_id yang ditemukan
            $roomTypeCounts = $allRooms->groupBy('room_type_id')->map->count();
            Log::info("📊 Room type distribution: " . json_encode($roomTypeCounts));

            // Map room_type_id ke service type - FIXED VERSION
            $mappedRooms = $allRooms->map(function ($room) {
                $service = $this->mapRoomTypeToService($room->room_type_id);
                $roomType = $this->getRoomTypeName($room->room_type_id);
                
                // Debug setiap room
                Log::info("🔍 Room {$room->number}: type_id={$room->room_type_id} → service={$service}");

                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'room_type_id' => $room->room_type_id,
                    'service' => $service,
                    'room_type' => $roomType,
                    'floor' => $room->floor,
                    'capacity' => $room->capacity,
                    'size_m2' => $room->size_m2,
                    'base_status' => $room->base_status,
                    'location_id' => $room->location_id
                ];
            });

            // Debug final service distribution
            $serviceCounts = $mappedRooms->groupBy('service')->map->count();
            Log::info("🎯 Final service distribution: " . json_encode($serviceCounts));

            return response()->json([
                'success' => true,
                'data' => $mappedRooms,
                'count' => $mappedRooms->count(),
                'debug' => [
                    'room_type_distribution' => $roomTypeCounts,
                    'service_distribution' => $serviceCounts
                ],
                'message' => "Loaded {$mappedRooms->count()} rooms from database"
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error in getUniqueRooms: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error fetching rooms from database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map room_type_id ke service type
     */
        private function mapRoomTypeToService($roomTypeId)
    {
        // Berdasarkan data Anda:
        // - room_type_id = 1 → Private Office (rooms 301-307)
        // - room_type_id = 6 → Sharing Room (room 308) 
        // - room_type_id = NULL → Meeting Room (rooms 202-205)
        
        $mapping = [
            '1' => 'private-office',   // 1 = Private Office  
            '6' => 'sharing-room',     // 6 = Sharing Room
        ];

        // Handle NULL dan nilai lainnya
        if ($roomTypeId === null) {
            return 'meeting-room';
        }

        return $mapping[(string)$roomTypeId] ?? 'meeting-room';
    }

    /**
     * Get room type name untuk display
     */
    private function getRoomTypeName($roomTypeId)
    {
        $mapping = [
            '1' => 'Private Office',
            '6' => 'Sharing Room',
        ];

        if ($roomTypeId === null) {
            return 'Meeting Room';
        }

        return $mapping[(string)$roomTypeId] ?? 'Meeting Room';
    }

    /**
     * Get room status real-time dari transactions
     */
    public function getRoomStatus($roomId)
    {
        try {
            // Validasi roomId
            if (!is_numeric($roomId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid room ID'
                ], 400);
            }

            $now = now();
            $currentDate = $now->format('Y-m-d');
            $currentTime = $now->format('H:i:s');

            Log::info("🔄 Checking status for room: {$roomId} at {$currentDate} {$currentTime}");

            // 1. Check OCCUPIED
            $occupiedQuery = DB::table('transactions')
                ->where('room_id', $roomId)
                ->where('booking_date', $currentDate)
                ->where('start_time', '<=', $currentTime)
                ->whereIn('status', ['settlement', 'capture', 'pending'])
                ->select('id', 'booking_date', 'start_time', 'jam', 'nama_lengkap', 'status');

            $occupied = $occupiedQuery->get()
                ->filter(function ($transaction) use ($currentTime) {
                    try {
                        $startTime = strtotime($transaction->start_time);
                        $endTime = $startTime + ($transaction->jam * 3600);
                        $currentTimeStamp = strtotime($currentTime);
                        return $currentTimeStamp <= $endTime;
                    } catch (\Exception $e) {
                        Log::error("Error calculating occupancy: " . $e->getMessage());
                        return false;
                    }
                })
                ->first();

            if ($occupied) {
                $startTime = strtotime($occupied->start_time);
                $endTime = $startTime + ($occupied->jam * 3600);
                $endTimeFormatted = date('H:i:s', $endTime);

                Log::info("✅ Room {$roomId} is OCCUPIED until {$endTimeFormatted}");

                return response()->json([
                    'status' => 'occupied',
                    'type' => 'current',
                    'booking' => $occupied,
                    'message' => 'Room is currently occupied',
                    'until' => $endTimeFormatted
                ]);
            }

            // 2. Check TODAY'S BOOKINGS
            $todaysBooking = DB::table('transactions')
                ->where('room_id', $roomId)
                ->where('booking_date', $currentDate)
                ->where('start_time', '>', $currentTime)
                ->whereIn('status', ['settlement', 'capture', 'pending'])
                ->orderBy('start_time', 'asc')
                ->select('id', 'booking_date', 'start_time', 'jam', 'nama_lengkap', 'status')
                ->first();

            if ($todaysBooking) {
                Log::info("✅ Room {$roomId} is BOOKED for today at {$todaysBooking->start_time}");
                return response()->json([
                    'status' => 'booked',
                    'type' => 'today',
                    'booking' => $todaysBooking,
                    'message' => 'Room is booked for today',
                    'starts' => $todaysBooking->start_time
                ]);
            }

            // 3. Check FUTURE BOOKINGS
            $futureBooking = DB::table('transactions')
                ->where('room_id', $roomId)
                ->where('booking_date', '>', $currentDate)
                ->whereIn('status', ['settlement', 'capture', 'pending'])
                ->orderBy('booking_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->select('id', 'booking_date', 'start_time', 'jam', 'nama_lengkap', 'status')
                ->first();

            if ($futureBooking) {
                Log::info("✅ Room {$roomId} has FUTURE booking on {$futureBooking->booking_date}");
                return response()->json([
                    'status' => 'booked',
                    'type' => 'future',
                    'booking' => $futureBooking,
                    'message' => 'Room has future booking',
                    'on_date' => $futureBooking->booking_date
                ]);
            }

            // 4. AVAILABLE
            Log::info("✅ Room {$roomId} is AVAILABLE");
            return response()->json([
                'status' => 'available',
                'type' => 'free',
                'booking' => null,
                'message' => 'Room is available'
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error in getRoomStatus for room {$roomId}: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching room status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update room data (SIMPLIFIED VERSION)
     */
    public function updateRoom(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Log::info("🔄 Updating room ID: {$id}", $request->all());

            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            $validated = $request->validate([
                'room_number' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|required|in:available,occupied,maintenance,booked',
                'floor' => 'nullable|integer',
                'capacity' => 'nullable|integer',
                'size_m2' => 'nullable|numeric',
                'location_id' => 'required|integer|exists:locations,id',
                'notes' => 'nullable|string'
            ]);

            Log::info("✅ Validated data:", $validated);

            // Update room data - hanya field yang ada di database
            $updateData = [];
            
            if (isset($validated['room_number'])) {
                // Check if room number is unique
                $existingRoom = Room::where('room_number', $validated['room_number'])
                                    ->where('id', '!=', $id)
                                    ->first();
                if ($existingRoom) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Room number already exists'
                    ], 422);
                }
                $updateData['room_number'] = $validated['room_number'];
            }

            if (isset($validated['status'])) {
                $updateData['status'] = $validated['status'];
            }

            // HAPUS bagian mapping service jika tidak diperlukan
            // if (isset($validated['service'])) {
            //     $roomTypeId = $this->mapServiceToRoomTypeId($validated['service']);
            //     $updateData['room_type_id'] = $roomTypeId;
            // }

            Log::info("📝 Update data to save:", $updateData);

            // Update the room
            $room->update($updateData);

            DB::commit();

            Log::info("✅ Room {$room->room_number} updated successfully");

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error updating room {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating room: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set maintenance status for room
     */
    public function setMaintenance(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Log::info("🔄 Setting maintenance for room ID: {$id}", $request->all());

            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            // Validasi yang lebih fleksibel
            $validated = $request->validate([
                'status' => 'required|in:maintenance,available',
                'maintenance_start' => 'required_if:status,maintenance|date',
                'maintenance_end' => 'nullable|date|after_or_equal:maintenance_start', // ✅ UBAH: after_or_equal
                'maintenance_reason' => 'required_if:status,maintenance|string|max:500'
            ]);

            Log::info("✅ Validated data for maintenance:", $validated);

            // Prepare update data
            $updateData = ['status' => $validated['status']];

            if ($validated['status'] === 'maintenance') {
                $updateData['maintenance_start'] = $validated['maintenance_start'];
                $updateData['maintenance_end'] = $validated['maintenance_end'] ?? null;
                $updateData['maintenance_reason'] = $validated['maintenance_reason'];
            } else {
                $updateData['maintenance_start'] = null;
                $updateData['maintenance_end'] = null;
                $updateData['maintenance_reason'] = null;
            }

            Log::info("📝 Maintenance update data:", $updateData);

            // Update the room
            $room->update($updateData);

            DB::commit();

            $action = $validated['status'] === 'maintenance' ? 'set to maintenance' : 'released from maintenance';
            Log::info("✅ Room {$room->room_number} {$action}");

            return response()->json([
                'success' => true,
                'message' => "Room {$action} successfully",
                'data' => $room
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error("❌ Validation error in maintenance for room {$id}: " . json_encode($e->errors()));
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error setting maintenance for room {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error setting maintenance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete room
     */
    public function deleteRoom($id)
    {
        try {
            Log::info("🔄 Deleting room ID: {$id}");

            $room = Room::find($id);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            // Check if room has active bookings
            $activeBookings = DB::table('transactions')
                ->where('room_id', $id)
                ->where(function($query) {
                    $query->where('booking_date', '>=', now()->format('Y-m-d'))
                          ->orWhere(function($q) {
                              $q->where('booking_date', now()->format('Y-m-d'))
                                ->where('start_time', '>=', now()->format('H:i:s'));
                          });
                })
                ->whereIn('status', ['settlement', 'capture', 'pending'])
                ->exists();

            if ($activeBookings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete room with active or future bookings'
                ], 400);
            }

            $roomNumber = $room->room_number;
            $room->delete();

            Log::info("✅ Room {$roomNumber} deleted successfully");

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error deleting room {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting room: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new room
     */
    public function createRoom(Request $request)
    {
        DB::beginTransaction();
        try {
            Log::info("🔄 Creating new room", $request->all());

            $validated = $request->validate([
                'room_number' => 'required|string|max:255|unique:rooms,room_number',
                'status' => 'required|in:available,occupied,maintenance,booked',
                'service' => 'required|string',
                'room_type_id' => 'nullable|integer',
                'floor' => 'nullable|integer',
                'capacity' => 'nullable|integer',
                'size_m2' => 'nullable|numeric',
                'location_id' => 'required|integer|exists:locations,id',
                'notes' => 'nullable|string'
            ]);

            Log::info("✅ Validated data for create:", $validated);

            // Map service to room_type_id
            $roomTypeId = $this->mapServiceToRoomTypeId($validated['service']);
            
            $roomData = [
                'room_number' => $validated['room_number'],
                'status' => $validated['status'],
                'room_type_id' => $roomTypeId,
                'floor' => $validated['floor'] ?? null,
                'capacity' => $validated['capacity'] ?? null,
                'size_m2' => $validated['size_m2'] ?? null,
                'location_id' => $validated['location_id'],
            ];

            $room = Room::create($roomData);

            DB::commit();

            Log::info("✅ Room {$room->room_number} created successfully");

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error("❌ Validation error creating room: " . json_encode($e->errors()));
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error creating room: " . $e->getMessage());
            Log::error("❌ Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating room: ' . $e->getMessage()
            ], 500);
        }
    }

    private function mapServiceToRoomTypeId($service)
    {
        $mapping = [
            'private-office' => 1,
            'sharing-room' => 6,
            'meeting-room' => null,
        ];

        return $mapping[$service] ?? null;
    }
}