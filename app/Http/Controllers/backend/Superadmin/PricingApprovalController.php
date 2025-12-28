<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ServicePrice;
use App\Models\Location;
use App\Models\RoomType;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingApprovalController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        // Apply auth middleware ke semua method
        $this->middleware('auth');
        
        // Apply superadmin middleware ke semua method
        // Pastikan middleware 'superadmin' sudah dibuat
        $this->middleware('superadmin'); 
    }
    
    /**
     * Format duration untuk display
     */
    private function formatDuration($record)
    {
        if (!$record->duration || !$record->duration_type) {
            return 'N/A';
        }
        
        $units = [
            'hours' => 'jam',
            'days' => 'hari', 
            'months' => 'bulan',
            'years' => 'tahun',
            'minutes' => 'menit'
        ];
        
        $unit = $units[$record->duration_type] ?? $record->duration_type;
        return "{$record->duration} {$unit}";
    }
    
    /**
     * GET: /api/superadmin/pricing/pending
     */
    public function getPendingRequests(Request $request)
    {
        try {
            \Log::info('Fetching pending requests', ['user_id' => auth()->id()]);
            
            // LOAD SEMUA RELASI YANG DIBUTUHKAN
            $pendingRequests = ServicePrice::with([
                'parent', 
                'location.city',
                'roomType',      // ✅ Relasi roomType
                'category',      // ✅ Relasi category
                'room'           // ✅ Relasi room (optional)
            ])
                ->where('request_status', 'pending')
                ->orderBy('requested_at', 'desc')
                ->limit(20)
                ->get();
            
            // TRANSFORMASI menggunakan data dari relasi
            $transformedData = $pendingRequests->map(function($record) {
                $parentPrice = $record->parent ? $record->parent->base_price : null;
                $priceChange = null;
                
                if ($parentPrice && $parentPrice > 0) {
                    $priceChange = (($record->base_price - $parentPrice) / $parentPrice) * 100;
                }
                
                return [
                    // Basic info
                    'id' => $record->id,
                    'parent_id' => $record->parent_id,
                    
                    // ✅ Service Type - AMBIL DARI RELASI
                    'room_type' => $record->roomType->name ?? 'N/A',
                    'category' => $record->category->name ?? 'General',
                    'room_name' => $record->room->name ?? null, // Optional
                    
                    // Pricing
                    'price' => $record->base_price,
                    'price_formatted' => $record->price_formatted,
                    'previous_price' => $parentPrice,
                    'previous_price_formatted' => $parentPrice 
                        ? 'Rp ' . number_format($parentPrice, 0, ',', '.') 
                        : null,
                    'price_change_percent' => $priceChange ? round($priceChange, 1) : null,
                    'price_change_display' => $priceChange 
                        ? ($priceChange > 0 ? '+' : '') . round($priceChange, 1) . '%'
                        : null,
                    
                    // Duration
                    'duration_display' => $record->duration_display,
                    'duration_type' => $record->duration_type,
                    'duration' => $record->duration,
                    
                    // Location info
                    'location_id' => $record->location_id,
                    'location_name' => $record->location ? $record->location->name : null,
                    'city_name' => $record->location && $record->location->city 
                        ? $record->location->city->name 
                        : null,
                    
                    // Request info
                    'request_reason' => $record->request_reason,
                    'requested_by' => $record->requested_by,
                    'requested_at' => $record->requested_at 
                        ? $record->requested_at->format('Y-m-d H:i') 
                        : null,
                    'status' => $record->request_status,
                    
                    // Additional
                    'coffee_break_option' => $record->coffee_break_option,
                    'deposit' => $record->deposit,
                ];
            });
            
            \Log::info('Pending requests fetched', ['count' => $pendingRequests->count()]);
            
            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'count' => $pendingRequests->count(),
                'debug' => [
                    'query_executed' => true,
                    'records_found' => $pendingRequests->count(),
                    'user_role' => auth()->user()->role ?? 'unknown'
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('API Error - getPendingRequests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending requests',
                'error' => $e->getMessage(),
                'debug_trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
    
    /**
     * Transform single request data
     */
    private function transformRequestData($record)
    {
        // Room data
        $roomData = null;
        if ($record->room) {
            $roomData = [
                'id' => $record->room->id,
                'room_number' => $record->room->room_number,
                'floor' => $record->room->floor,
                'capacity' => $record->room->capacity,
            ];
        }
        
        // Parent data
        $parentData = null;
        $priceChange = null;
        $priceChangeDisplay = null;
        
        if ($record->parent) {
            $parentData = [
                'id' => $record->parent->id,
                'price' => (float) $record->parent->base_price,
                'price_formatted' => $record->parent->price_formatted, // ✅ Pakai accessor
                'duration' => $record->parent->duration,
                'duration_type' => $record->parent->duration_type,
                'duration_display' => $record->parent->duration_display, // ✅ Pakai accessor
                'coffee_break_price' => $record->parent->coffee_break_price,
                'deposit' => $record->parent->deposit,
            ];
            
            // Calculate price change (tetap di controller karena business logic)
            if ($record->parent->base_price > 0) {
                $oldPrice = (float) $record->parent->base_price;
                $newPrice = (float) $record->base_price;
                $priceChange = (($newPrice - $oldPrice) / $oldPrice) * 100;
                $priceChangeDisplay = ($priceChange > 0 ? "+" : "") . number_format($priceChange, 1) . "%";
            }
        }
        
        return [
            'id' => $record->id,
            'parent_id' => $record->parent_id,
            
            // Location info
            'location' => $record->location ? [
                'id' => $record->location->id,
                'name' => $record->location->name,
                'address' => $record->location->address,
                'city' => $record->location->city ? $record->location->city->name : null,
            ] : null,
            'location_id' => $record->location_id,
            
            // Room info
            'room' => $roomData,
            'room_id' => $record->room_id,
            
            // Category & Type
            'room_type' => $record->roomType ? $record->roomType->name : 'N/A',
            'room_type_id' => $record->room_type_id,
            'category' => $record->category ? $record->category->name : 'General',
            'category_id' => $record->service_category_id,
            
            // Duration - PAKAI ACCESSOR ✅
            'duration_display' => $record->duration_display,
            'duration_type' => $record->duration_type,
            'duration' => $record->duration,
            
            // Pricing - PAKAI ACCESSOR untuk formatted price ✅
            'price' => (float) $record->base_price,
            'price_formatted' => $record->price_formatted,
            'previous_price' => (float) $record->previous_price,
            'previous_price_formatted' => $record->previous_price 
                ? 'Rp ' . number_format($record->previous_price, 0, ',', '.') 
                : null,
            'price_change_percent' => $priceChange,
            'price_change_display' => $priceChangeDisplay,
            
            // Additional pricing info
            'coffee_break_option' => $record->coffee_break_option,
            'coffee_break_price' => $record->coffee_break_price,
            'deposit' => $record->deposit,
            
            // Status & Request info
            'status' => $record->request_status,
            'reason' => $record->request_reason,
            'requested_by' => $record->requested_by,
            'requested_at' => $record->requested_at ? $record->requested_at->format('Y-m-d H:i') : null,
            
            // For comparison
            'parent_data' => $parentData,
            
            // Flags
            'has_coffee_break' => !empty($record->coffee_break_option) && 
                                $record->coffee_break_option !== 'excluded',
            'has_deposit' => !empty($record->deposit) && $record->deposit > 0,
        ];
    }
    
    /**
     * POST: /api/superadmin/pricing/{id}/approve
     */
    public function approveRequest($id)
    {
        try {
            DB::beginTransaction();
            
            $pendingRequest = ServicePrice::with('parent')->findOrFail($id);
            
            if ($pendingRequest->request_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be approved'
                ], 400);
            }
            
            // Deactivate parent jika ada
            if ($pendingRequest->parent) {
                $pendingRequest->parent->update([
                    'request_status' => 'inactive',
                    'updated_at' => now()
                ]);
            }
            
            // ✅ PERBAIKAN: Simpan NAMA user, bukan ID
            $user = auth()->user();
            $reviewerName = $user ? $user->name : 'System';
            
            // Approve current request
            $pendingRequest->update([
                'request_status' => 'active',
                'reviewed_by' => $reviewerName, // ✅ Simpan nama, bukan ID
                'reviewed_at' => now(),
            ]);
            
            DB::commit();
            
            Log::info('Price request approved', [
                'request_id' => $id,
                'user_id' => auth()->id(),
                'user_name' => $reviewerName,
                'new_price' => $pendingRequest->base_price
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Price request approved successfully',
                'data' => [
                    'id' => $pendingRequest->id,
                    'status' => 'active',
                    'price' => $pendingRequest->base_price,
                    'reviewed_by' => $reviewerName
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Approve price error', [
                'request_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * POST: /api/superadmin/pricing/{id}/reject
     */
    public function rejectRequest(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'reason' => 'required|string|min:10|max:500'
            ]);
            
            $pendingRequest = ServicePrice::findOrFail($id);
            
            if ($pendingRequest->request_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be rejected'
                ], 400);
            }
            
            // ✅ PERBAIKAN: Simpan NAMA user
            $user = auth()->user();
            $reviewerName = $user ? $user->name : 'System';
            
            $pendingRequest->update([
                'request_status' => 'rejected',
                'request_reason' => $validated['reason'],
                'reviewed_by' => $reviewerName, // ✅ Simpan nama
                'reviewed_at' => now(),
            ]);
            
            Log::info('Price request rejected', [
                'request_id' => $id,
                'user_name' => $reviewerName,
                'reason' => substr($validated['reason'], 0, 100)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Price request rejected',
                'data' => [
                    'id' => $pendingRequest->id,
                    'status' => 'rejected',
                    'reviewed_by' => $reviewerName
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reject price error', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * GET: /api/superadmin/pricing/{id}/details
     */
    public function getRequestDetails($id)
    {
        try {
            $request = ServicePrice::with([
                'parent',
                'location',
                'room',
                'roomType',
                'category',
            ])->findOrFail($id);
            
            $data = $this->transformRequestData($request);
            
            // Add additional details for modal
            $data['additional_details'] = [
                'coffee_break_option_label' => $this->getCoffeeBreakLabel($request->coffee_break_option),
                'duration_type_label' => $this->getDurationTypeLabel($request->duration_type),
                'created_at' => $request->created_at?->format('Y-m-d H:i'),
                'updated_at' => $request->updated_at?->format('Y-m-d H:i'),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get request details error', [
                'request_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }
    }
    
    /**
     * Helper: Get coffee break label
     */
    private function getCoffeeBreakLabel($option)
    {
        $labels = [
            'included' => 'Termasuk',
            'optional' => 'Opsional',
            'excluded' => 'Tidak Termasuk',
            null => 'Tidak Ada'
        ];
        
        return $labels[$option] ?? 'Tidak Diketahui';
    }
    
    /**
     * Helper: Get duration type label
     */
    private function getDurationTypeLabel($type)
    {
        $labels = [
            'minutes' => 'Menit',
            'hours' => 'Jam',
            'days' => 'Hari',
            'months' => 'Bulan',
            'years' => 'Tahun'
        ];
        
        return $labels[$type] ?? $type;
    }

    // Controller method baru untuk mendapatkan filter options
    public function getFilterOptions()
    {
        try {
            // Ambil SEMUA locations dari database (bukan hanya yang ada di pending)
            $locations = Location::with('city')
                ->select('id', 'name', 'city_id')
                ->orderBy('name')
                ->get()
                ->map(function($location) {
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'city_name' => $location->city->name ?? null
                    ];
                });
            
            // Ambil SEMUA room types dari database
            $roomTypes = RoomType::select('id', 'name')
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'locations' => $locations,
                'roomTypes' => $roomTypes
            ]);
            
        } catch (\Exception $e) {
            \Log::error('API Error - getFilterOptions', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load filter options'
            ], 500);
        }
    }

    /**
     * GET: /superadmin/api/pricing/requests
     * Menampilkan request dengan status: active, inactive, rejected
     * TIDAK menampilkan pending
     */
    public function getRequestsByStatus(Request $request)
    {
        try {
            // Validasi - SESUAI dengan values request_status
            $validated = $request->validate([
                'status' => 'nullable|in:active,inactive,rejected,all', // ✅ SESUAI dengan database
                'location_id' => 'nullable|exists:locations,id',
                'room_type_id' => 'nullable|exists:room_types,id',
                'category_id' => 'nullable|exists:service_categories,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'sort_by' => 'nullable|in:created_at,updated_at,base_price,requested_at,reviewed_at',
                'sort_order' => 'nullable|in:asc,desc',
                'per_page' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:100',
            ]);

            $status = $validated['status'] ?? 'all';
            $perPage = $validated['per_page'] ?? 20;
            $sortBy = $validated['sort_by'] ?? 'reviewed_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';

            // Query dasar - TIDAK termasuk pending
            $query = ServicePrice::with([
                'parent',
                'location.city',
                'roomType',
                'category',
                'room'
            ]);

            // ✅ SELALU EXCLUDE PENDING (karena punya endpoint sendiri)
            $query->where('request_status', '!=', 'pending');

            // Filter berdasarkan status yang diminta
            if ($status !== 'all') {
                $query->where('request_status', $status); // ✅ langsung pakai request_status
            }

            // Filter location
            if (!empty($validated['location_id'])) {
                $query->where('location_id', $validated['location_id']);
            }

            // Filter room type
            if (!empty($validated['room_type_id'])) {
                $query->where('room_type_id', $validated['room_type_id']);
            }

            // Filter category
            if (!empty($validated['category_id'])) {
                $query->where('service_category_id', $validated['category_id']);
            }

            // Filter date range (gunakan reviewed_at untuk yang sudah diproses)
            if (!empty($validated['date_from'])) {
                $query->whereDate('reviewed_at', '>=', $validated['date_from']);
            }

            if (!empty($validated['date_to'])) {
                $query->whereDate('reviewed_at', '<=', $validated['date_to']);
            }

            // Search
            if (!empty($validated['search'])) {
                $searchTerm = '%' . $validated['search'] . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->whereHas('location', function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', $searchTerm)
                        ->orWhereHas('city', function($q) use ($searchTerm) {
                            $q->where('name', 'LIKE', $searchTerm);
                        });
                    })
                    ->orWhereHas('roomType', function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', $searchTerm);
                    })
                    ->orWhereHas('category', function($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', $searchTerm);
                    })
                    ->orWhere('request_reason', 'LIKE', $searchTerm)
                    ->orWhere('review_comment', 'LIKE', $searchTerm);
                });
            }

            // Sorting - default by reviewed_at (waktu diproses)
            switch ($sortBy) {
                case 'base_price':
                    $query->orderBy('base_price', $sortOrder);
                    break;
                case 'requested_at':
                    $query->orderBy('requested_at', $sortOrder);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                case 'updated_at':
                    $query->orderBy('updated_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('reviewed_at', $sortOrder);
            }

            // Pagination
            $requests = $query->paginate($perPage);

            // Transform data
            $transformedData = $requests->map(function($record) {
                $data = $this->transformRequestData($record);
                
                // Status info lengkap
                $data['status_info'] = [
                    'request_status' => $record->request_status,
                    'is_active' => $record->request_status === 'active',
                    'is_inactive' => $record->request_status === 'inactive',
                    'is_rejected' => $record->request_status === 'rejected',
                    'reviewed_at' => $record->reviewed_at?->format('Y-m-d H:i'),
                    'reviewed_by' => $record->reviewed_by,
                    'review_comment' => $record->review_comment,
                ];
                
                return $data;
            });

            // Hitung statistik - HANYA active, inactive, rejected (tidak pending)
            $stats = [
                'all' => ServicePrice::where('request_status', '!=', 'pending')->count(),
                'active' => ServicePrice::where('request_status', 'active')->count(),
                'inactive' => ServicePrice::where('request_status', 'inactive')->count(),
                'rejected' => ServicePrice::where('request_status', 'rejected')->count(),
            ];

            Log::info('Processed pricing requests fetched', [
                'status_filter' => $status,
                'count' => $requests->total(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'meta' => [
                    'pagination' => [
                        'total' => $requests->total(),
                        'per_page' => $requests->perPage(),
                        'current_page' => $requests->currentPage(),
                        'last_page' => $requests->lastPage(),
                    ],
                    'stats' => $stats,
                    'current_filter' => $status,
                    'sorting' => [
                        'by' => $sortBy,
                        'order' => $sortOrder
                    ],
                    'note' => 'Pending requests are excluded from this endpoint. Use /pending for pending requests.'
                ],
                'filters_available' => [
                    'status_options' => [
                        ['value' => 'all', 'label' => 'All Processed', 'color' => 'gray'],
                        ['value' => 'active', 'label' => 'Active Prices', 'color' => 'green'],
                        ['value' => 'inactive', 'label' => 'Inactive Prices', 'color' => 'yellow'],
                        ['value' => 'rejected', 'label' => 'Rejected Requests', 'color' => 'red'],
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get processed pricing requests error', [
                'error' => $e->getMessage(),
                'params' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pricing requests',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}