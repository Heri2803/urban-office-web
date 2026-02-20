<?php
namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Room;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomAssignController extends Controller
{
    public function index()
    {
        // ✅ AMBIL SEMUA SERVICE CATEGORIES TANPA FILTER STATUS
        $serviceCategories = ServiceCategory::all();
        return view('layouts.admin.room-assignment', compact('serviceCategories'));
    }

    public function getRoomsWithStatus(Request $request)
    {
        try {
            $rooms = Room::with(['transactions' => function($query) {
                $query->where('status', 'settlement')
                    ->where(function($q) {
                        $now = Carbon::now();
                        $q->whereDate('booking_date', '>=', $now->copy()->subDays(2)->toDateString());
                    })
                    ->orderBy('booking_date', 'asc')
                    ->orderBy('start_time', 'asc');
            }, 'roomType', 'location'])->get();

            $formattedRooms = $rooms->map(function($room) {
                return $this->formatRoomData($room);
            });

            $uniqueRoomTypes = $rooms->pluck('roomType')
                ->filter()
                ->unique('id')
                ->values();

            $formattedTabs = [];
            
            $meetingRooms = $formattedRooms->filter(function($room) {
                return $room['room_type_id'] === null;
            });
            
            if ($meetingRooms->count() > 0) {
                $formattedTabs[] = [
                    'id' => 'meeting-room',
                    'name' => 'Meeting Room',
                    'icon' => '🚪',
                    'rooms' => $meetingRooms->values()
                ];
            }

            foreach ($uniqueRoomTypes as $roomType) {
                $roomsForType = $formattedRooms->filter(function($room) use ($roomType) {
                    return $room['room_type_id'] == $roomType->id;
                });

                if ($roomsForType->count() > 0) {
                    $formattedTabs[] = [
                        'id' => 'room-type-' . $roomType->id,
                        'name' => $roomType->name,
                        'icon' => $this->getRoomTypeIcon($roomType->name),
                        'rooms' => $roomsForType->values()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'service_categories' => $formattedTabs,
                'all_rooms' => $formattedRooms,
                'timestamp' => Carbon::now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            \Log::error('Room Assignment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load rooms data: ' . $e->getMessage()
            ], 500);
        }
    }

    
    private function formatRoomDataForRealTime($room, $lightMode = false)
    {
        $activeTransaction = $room->transactions->first();
        
        $roomData = [
            'id' => $room->id,
            'number' => $room->room_number,
            'room_type_id' => $room->room_type_id,
            'room_type' => $room->roomType ? $room->roomType->name : 'Private Office',
            'status' => $room->status, // Base status from DB
            'location' => $room->location ? $room->location->name : 'Unknown',
            'capacity' => $room->capacity,
            'service_id' => $room->room_type_id === null 
                ? 'meeting-room' 
                : 'room-type-' . $room->room_type_id
        ];

        // ✅ Untuk light mode: hanya kirim data booking, frontend handle status
        if ($activeTransaction) {
            $roomData['booking'] = [
                'orderId' => $activeTransaction->order_id,
                'customerName' => $activeTransaction->nama_lengkap,
                'email' => $activeTransaction->email,
                'phone' => $activeTransaction->phone,
                'startDateTime' => Carbon::parse($activeTransaction->booking_date . ' ' . $activeTransaction->start_time)->toIso8601String(),
                'endDateTime' => $this->calculateBookingEndTime($activeTransaction)->toIso8601String(),
                'durationText' => $this->formatDuration($activeTransaction),
                'bookingDate' => $activeTransaction->booking_date,
                'startTime' => $activeTransaction->start_time
            ];
            
            // ❌ HAPUS calculated status untuk light mode - frontend akan handle
            if (!$lightMode) {
                // Untuk kompatibilitas, berikan initial calculation
                $calculatedStatus = $this->calculateStatus($activeTransaction);
                $roomData['status'] = $calculatedStatus;
                
                $remainingData = $this->calculateRemainingTime($activeTransaction);
                $roomData['remainingTime'] = $remainingData['formatted'];
                $roomData['remainingMinutes'] = $remainingData['minutes'];
            }
        }

        return $roomData;
    }

    private function getRoomTypeIcon($roomTypeName)
    {
        $icons = [
            'Sharing Room' => '👥',
            'Multi Purpose' => '🏢',
            'Virtual Office' => '💻',
            'Event Space' => '🎪'
        ];

        return $icons[$roomTypeName] ?? '🏢';
    }

    private function formatRoomData($room)
    {
        $activeTransaction = $room->transactions->first();
        
        $roomData = [
            'id' => $room->id,
            'number' => $room->room_number,
            'room_type_id' => $room->room_type_id,
            'room_type' => $room->roomType ? $room->roomType->name : 'Private Office',
            'status' => $room->status,
            'remainingTime' => null,
            'remainingMinutes' => null,
            'booking' => null,
            'location' => $room->location ? $room->location->name : 'Unknown',
            'capacity' => $room->capacity,
            'service_id' => $room->room_type_id === null 
                ? 'meeting-room' 
                : 'room-type-' . $room->room_type_id
        ];

        // Jika ada maintenance, skip booking calculation
        if ($room->status == 'maintenance') {
            return $roomData;
        }

        // ✅ Jika ada transaction, hitung status real-time
        if ($activeTransaction) {
            $calculatedStatus = $this->calculateStatus($activeTransaction);
            
            // ✅ Override status dengan calculated status
            $roomData['status'] = $calculatedStatus;
            
            // ✅ Hanya kirim booking data jika booked atau occupied
            if (in_array($calculatedStatus, ['booked', 'occupied'])) {
                $roomData['booking'] = $this->formatBookingData($activeTransaction);
                
                // ✅ Hitung remaining time
                $remainingData = $this->calculateRemainingTime($activeTransaction);
                $roomData['remainingTime'] = $remainingData['formatted'];
                $roomData['remainingMinutes'] = $remainingData['minutes'];
            }
        }

        return $roomData;
    }

        private function formatBookingData($transaction)
    {
        $startDateTime = $startDateTime = Carbon::parse($transaction->booking_date_only . ' ' . $transaction->start_time);;
        $endDateTime = $this->calculateBookingEndTime($transaction);
        
        return [
            'orderId' => $transaction->order_id,
            'customerName' => $transaction->nama_lengkap,
            'email' => $transaction->email,
            'phone' => $transaction->phone,
            'bookingDate' => $transaction->booking_date,
            'startTime' => $transaction->start_time,
            'startDateTime' => $startDateTime->toIso8601String(), // ✅ BARU
            'endDateTime' => $endDateTime->toIso8601String(), // ✅ BARU
            'duration' => [
                'jam' => $transaction->jam ?? 0,
                'hari' => $transaction->hari ?? 0,
                'minggu' => $transaction->minggu ?? 0,
                'bulan' => $transaction->bulan ?? 0,
                'tahun' => $transaction->tahun ?? 0
            ],
            'durationText' => $this->formatDuration($transaction), // ✅ BARU
            'jumlahOrang' => $transaction->jumlah_orang,
            'paket' => $transaction->paket,
            'totalAmount' => $transaction->gross_amount + $transaction->lunch_total
        ];
    }

    private function calculateStatus($transaction)
    {
        $now = Carbon::now();
        $startDateTime = $startDateTime = Carbon::parse($transaction->booking_date_only . ' ' . $transaction->start_time);;
        $endDateTime = $this->calculateBookingEndTime($transaction);

        if ($now->lt($startDateTime)) {
            return 'booked';
        } elseif ($now->lte($endDateTime)) {
            return 'occupied';
        } else {
            return 'available';
        }
    }

    private function calculateBookingEndTime($transaction)
    {
        $startDateTime = $startDateTime = Carbon::parse($transaction->booking_date_only . ' ' . $transaction->start_time);;
        
        // Hitung total menit dari semua durasi
        $totalMinutes = 
            ($transaction->jam ?? 0) * 60 +
            ($transaction->hari ?? 0) * 24 * 60 +
            ($transaction->minggu ?? 0) * 7 * 24 * 60 +
            ($transaction->bulan ?? 0) * 30 * 24 * 60 +
            ($transaction->tahun ?? 0) * 365 * 24 * 60;

        return $startDateTime->copy()->addMinutes($totalMinutes);
    }

        private function calculateRemainingTime($transaction)
    {
        $now = Carbon::now();
        $startDateTime = $startDateTime = Carbon::parse($transaction->booking_date_only . ' ' . $transaction->start_time);;
        $endDateTime = $this->calculateBookingEndTime($transaction);

        // ✅ Untuk status "booked" (belum dimulai), hitung waktu sampai mulai
        if ($now->lt($startDateTime)) {
            $diffInMinutes = $now->diffInMinutes($startDateTime, false);
            
            if ($diffInMinutes <= 0) {
                return ['formatted' => null, 'minutes' => 0];
            }

            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;

            if ($days > 0) {
                return [
                    'formatted' => "Starts in {$days}d {$remainingHours}h",
                    'minutes' => $diffInMinutes
                ];
            } elseif ($hours > 0) {
                return [
                    'formatted' => "Starts in {$hours}h {$minutes}m",
                    'minutes' => $diffInMinutes
                ];
            } else {
                return [
                    'formatted' => "Starts in {$minutes}m",
                    'minutes' => $diffInMinutes
                ];
            }
        }

        // ✅ Untuk status "occupied" (sedang berlangsung), hitung waktu tersisa
        if ($now->lte($endDateTime)) {
            $diffInMinutes = $now->diffInMinutes($endDateTime, false);
            
            if ($diffInMinutes <= 0) {
                return ['formatted' => null, 'minutes' => 0];
            }

            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;

            if ($days > 0) {
                return [
                    'formatted' => "{$days}d {$remainingHours}h remaining",
                    'minutes' => $diffInMinutes
                ];
            } elseif ($hours > 0) {
                return [
                    'formatted' => "{$hours}h {$minutes}m remaining",
                    'minutes' => $diffInMinutes
                ];
            } else {
                return [
                    'formatted' => "{$minutes}m remaining",
                    'minutes' => $diffInMinutes
                ];
            }
        }

        return ['formatted' => null, 'minutes' => 0];
    }

    private function getServiceCategoryFromRoom($room)
    {
        // Logic untuk menentukan service category berdasarkan room type atau lainnya
        if ($room->roomType) {
            return $room->roomType->service_category_id ?? 1; // Default ke 1 jika tidak ada
        }
        
        return 1; // Default service category
    }

    private function getServiceIcon($serviceName)
    {
        $icons = [
            'Meeting Room' => '🏢',
            'Private Office' => '🚪', 
            'Sharing Room' => '👥',
            'Virtual Office' => '💻',
            'Event Space' => '🎪'
        ];

        return $icons[$serviceName] ?? '🏢';
    }

    public function getAvailableCustomers()
    {
        try {
            $customers = Transaction::where('status', 'settlement')
                ->where(function($query) {
                    $query->whereDate('booking_date', '>', now())
                          ->orWhere(function($q) {
                              $q->whereDate('booking_date', now()->toDateString())
                                ->where('start_time', '>', now()->format('H:i:s'));
                          });
                })
                ->with(['room', 'serviceCategory'])
                ->get()
                ->map(function($transaction) {
                    return [
                        'orderId' => $transaction->order_id,
                        'name' => $transaction->nama_lengkap,
                        'email' => $transaction->email,
                        'phone' => $transaction->phone,
                        'service_category_id' => $transaction->service_category_id,
                        'duration' => $this->formatDuration($transaction),
                        'checkIn' => $transaction->booking_date . ' ' . $transaction->start_time,
                        'price' => $transaction->gross_amount + $transaction->lunch_total,
                        'room_type' => $transaction->room_type,
                        'jumlah_orang' => $transaction->jumlah_orang
                    ];
                });

            return response()->json([
                'success' => true,
                'customers' => $customers
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Customers Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load customers'
            ], 500);
        }
    }

    private function formatDuration($transaction)
    {
        $parts = [];
        
        if ($transaction->jam > 0) $parts[] = $transaction->jam . ' jam';
        if ($transaction->hari > 0) $parts[] = $transaction->hari . ' hari';
        if ($transaction->minggu > 0) $parts[] = $transaction->minggu . ' minggu';
        if ($transaction->bulan > 0) $parts[] = $transaction->bulan . ' bulan';
        if ($transaction->tahun > 0) $parts[] = $transaction->tahun . ' tahun';
        
        return $parts ? implode(', ', $parts) : 'No duration';
    }

}