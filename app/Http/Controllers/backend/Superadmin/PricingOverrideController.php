<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Location;
use App\Models\ServiceCategory;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PricingOverrideController extends Controller
{
    /**
     * Display the price generation page
     * Middleware sudah handle authorization, jadi kita bisa asumsi user adalah superadmin
     */
    public function index()
    {
        // Karena sudah di-filter oleh middleware, semua user di sini adalah superadmin
        // Jadi tidak perlu cek role lagi
        return view('superadmin.pricing.branch-override', [
            'roomTypes' => RoomType::available()->get(),
            'locations' => Location::with(['city'])->get(), // Superadmin bisa lihat semua
            'serviceCategories' => ServiceCategory::all(),
        ]);
    }
    
    /**
     * API: Get form data (room types, locations, etc)
     * Middleware sudah pastikan hanya superadmin yang bisa akses
     */
        public function getFormData()
    {
        $user = Auth::user();
        
        // Superadmin bisa lihat semua locations
        $locations = Location::with(['city:id,name'])->get();
        
        // Ambil semua harga active dengan relasi room type
        $activePrices = ServicePrice::with('roomType:id,name')
            ->where('request_status', 'active')
            ->whereNotNull('location_id')
            ->whereNotNull('room_type_id')
            ->get(['id', 'location_id', 'room_type_id', 'duration_type', 'duration', 'base_price', 'coffee_break_option', 'coffee_break_price', 'deposit']);
        
        // Group prices by location_id
        $pricesGroupedByLocation = $activePrices->groupBy('location_id');
        
        // Get unique coffee break options from database with their prices
        $coffeeBreakOptions = ServicePrice::whereNotNull('coffee_break_option')
            ->where('coffee_break_option', '!=', 'none')
            ->select('coffee_break_option', 'coffee_break_price')
            ->distinct()
            ->get()
            ->map(function($item) {
                $label = match($item->coffee_break_option) {
                    'standard' => 'Standard',
                    'premium' => 'Premium',
                    default => ucfirst($item->coffee_break_option)
                };
                
                return [
                    'value' => $item->coffee_break_option,
                    'label' => $label,
                    'price' => (float) $item->coffee_break_price
                ];
            })
            ->values()
            ->toArray();
        
        // Add 'none' option
        array_unshift($coffeeBreakOptions, [
            'value' => 'none',
            'label' => 'Tidak Pakai Coffee Break',
            'price' => 0
        ]);
        
        return response()->json([
            'success' => true,
            'data' => [
                'roomTypes' => RoomType::available()
                    ->select('id', 'name')
                    ->get(),
                    
                'locations' => $locations->map(function($location) use ($pricesGroupedByLocation) {
                    $prices = $pricesGroupedByLocation->get($location->id, collect());
                    
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'city' => $location->city->name ?? 'N/A',
                        'full_name' => $location->name . ' - ' . ($location->city->name ?? ''),
                        'prices' => $prices->map(function($price) {
                            return [
                                'id' => $price->id,
                                'room_type_id' => $price->room_type_id,
                                'room_type_name' => $price->roomType->name ?? 'N/A',
                                'duration_type' => $price->duration_type,
                                'duration' => $price->duration,
                                'base_price' => (float) $price->base_price,
                                'coffee_break_option' => $price->coffee_break_option,
                                'coffee_break_price' => $price->coffee_break_price ? (float) $price->coffee_break_price : null,
                                'deposit' => $price->deposit ? (float) $price->deposit : null
                            ];
                        })
                    ];
                }),
                    
                'durationTypes' => [
                    ['value' => 'hours', 'label' => 'Per Jam'],
                    ['value' => 'days', 'label' => 'Per Hari'],
                    ['value' => 'months', 'label' => 'Per Bulan']
                ],
                
                'coffeeBreakOptions' => $coffeeBreakOptions,
                
                'currentUser' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'location_id' => $user->location_id
                ]
            ]
        ]);
    }
    
    
    /**
     * API: Submit price request to multiple locations
     * Superadmin bisa apply ke semua locations
     */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            // Room type & duration
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'duration_type' => 'required|in:hours,days,months',
            'duration' => 'required|integer|min:1',
            
            // Generated price
            'total_price' => 'required|numeric|min:10000',
            'coffee_break_option' => 'required|in:0,1,2', // 0=none, 1=standard, 2=premium
            'coffee_break_price' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            
            // Locations to apply
            'location_ids' => 'required|array|min:1',
            'location_ids.*' => 'exists:locations,id',
            
            // Request details
            'reason' => 'required|string|min:20|max:500',
            'effective_date' => 'nullable|date|after_or_equal:today',
            
            // Optional
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        $now = Carbon::now();
        
        // Tentukan status berdasarkan user role
        // Superadmin: langsung active, lainnya: pending
        $requestStatus = ($user->role === 'superadmin') ? 'active' : 'pending';
        
        DB::beginTransaction();
        
        try {
            $appliedCount = 0;
            $appliedLocations = [];
            
            foreach ($validated['location_ids'] as $locationId) {
                // Check if similar active price exists
                $existingPrice = ServicePrice::where([
                    'location_id' => $locationId,
                    'room_type_id' => $validated['room_type_id'],
                    'duration_type' => $validated['duration_type'],
                    'duration' => $validated['duration'],
                ])
                ->whereNull('parent_id')
                ->where('request_status', 'active') // Hanya cek yang active
                ->first();
                
                // Prepare price data
                $priceData = [
                    'location_id' => $locationId,
                    'room_type_id' => $validated['room_type_id'],
                    'room_id' => $validated['room_id'] ?? null,
                    'service_category_id' => $validated['service_category_id'] ?? null,
                    'duration_type' => $validated['duration_type'],
                    'duration' => $validated['duration'],
                    'base_price' => $validated['total_price'],
                    'coffee_break_option' => $validated['coffee_break_option'],
                    'coffee_break_price' => $validated['coffee_break_price'],
                    'deposit' => $validated['deposit'],
                    
                    // Superadmin: langsung active, bukan pending
                    'request_status' => $requestStatus,
                    'request_reason' => $validated['reason'],
                    'requested_by' => $user->name,
                    'requested_at' => $now,
                    'requested_by_user_id' => $user->id,
                    
                    // Untuk superadmin, auto-approve
                    'reviewed_by' => $requestStatus === 'active' ? $user->name : null,
                    'reviewed_at' => $requestStatus === 'active' ? $now : null,
                    'reviewed_by_user_id' => $requestStatus === 'active' ? $user->id : null,
                    
                    // Optional fields
                    'notes' => $validated['notes'] ?? null,
                ];
                
                // Add effective date if provided
                if (isset($validated['effective_date'])) {
                    $priceData['effective_date'] = Carbon::parse($validated['effective_date']);
                }
                
                // If updating existing price, create revision
                if ($existingPrice) {
                    $priceData['parent_id'] = $existingPrice->id;
                    $priceData['previous_price'] = $existingPrice->base_price;
                    
                    // Deactivate old price
                    $existingPrice->update(['request_status' => 'inactive']);
                }
                
                // Create the price
                ServicePrice::create($priceData);
                $appliedCount++;
                $appliedLocations[] = $locationId;
            }
            
            DB::commit();
            
            // Response message berdasarkan status
            $message = $requestStatus === 'active' 
                ? 'Harga berhasil diterapkan ke ' . $appliedCount . ' lokasi' 
                : 'Permintaan perubahan harga berhasil diajukan untuk ' . $appliedCount . ' lokasi';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'applied_count' => $appliedCount,
                    'request_status' => $requestStatus,
                    'requires_approval' => $requestStatus === 'pending',
                    'next_steps' => $requestStatus === 'active' 
                        ? 'Harga langsung aktif' 
                        : 'Menunggu approval superadmin'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses harga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Edit existing active price (Superadmin only)
     * Langsung apply changes, tidak butuh approval
     */
    public function editActivePrice(Request $request, $priceId)
    {
        $validated = $request->validate([
            // Field yang bisa di-edit
            'base_price' => 'required|numeric|min:50000',
            'coffee_break_price' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'reason' => 'required|string|min:10|max:500',
            'effective_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        $now = Carbon::now();
        
        // Get the active price to edit
        $activePrice = ServicePrice::where('id', $priceId)
            ->where('request_status', 'active')
            ->first();
        
        if (!$activePrice) {
            return response()->json([
                'success' => false,
                'message' => 'Harga tidak ditemukan atau tidak dalam status active'
            ], 404);
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate price change percentage
            $priceChangePercentage = 0;
            if ($activePrice->base_price > 0) {
                $priceChangePercentage = 
                    (($validated['base_price'] - $activePrice->base_price) / $activePrice->base_price) * 100;
            }
            
            // Prepare new price data (revision)
            $newPriceData = [
                // Copy semua field dari harga lama
                'parent_id' => $activePrice->id,
                'location_id' => $activePrice->location_id,
                'room_type_id' => $activePrice->room_type_id,
                'room_id' => $activePrice->room_id,
                'service_category_id' => $activePrice->service_category_id,
                'duration_type' => $activePrice->duration_type,
                'duration' => $activePrice->duration,
                
                // Updated values
                'base_price' => $validated['base_price'],
                'coffee_break_option' => $activePrice->coffee_break_option,
                'coffee_break_price' => $validated['coffee_break_price'],
                'deposit' => $validated['deposit'],
                
                // Request info
                'request_status' => 'active', // Langsung active
                'request_reason' => $validated['reason'],
                'requested_by' => $user->name,
                'requested_by_user_id' => $user->id,
                'requested_at' => $now,
                
                // Auto-approve for superadmin
                'reviewed_by' => $user->name,
                'reviewed_by_user_id' => $user->id,
                'reviewed_at' => $now,
                
                // Tracking changes
                'previous_price' => $activePrice->base_price,
                'price_change_percentage' => round($priceChangePercentage, 2),
                'is_edit_request' => true,
                'change_type' => 'edit',
                
                // Notes
                'notes' => $validated['notes'] ?? null,
                
                // Waktu Indonesia (sesuai config timezone app)
                'created_at' => $now,
                'updated_at' => $now,
            ];
            
            // Add effective date if provided (default: now)
            if (isset($validated['effective_date'])) {
                $newPriceData['effective_date'] = Carbon::parse($validated['effective_date']);
            } else {
                $newPriceData['effective_date'] = $now;
            }
            
            // Deactivate old price
            $activePrice->update([
                'request_status' => 'inactive',
                'updated_at' => $now
            ]);
            
            // Create new price record (revision)
            $newPrice = ServicePrice::create($newPriceData);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Harga berhasil diperbarui',
                'data' => [
                    'old_price_id' => $activePrice->id,
                    'new_price_id' => $newPrice->id,
                    'old_price' => number_format($activePrice->base_price, 0, ',', '.'),
                    'new_price' => number_format($newPrice->base_price, 0, ',', '.'),
                    'change_percentage' => $newPrice->price_change_percentage . '%',
                    'effective_date' => $newPrice->effective_date ? $newPrice->effective_date->format('d-m-Y H:i') : 'Segera',
                    'updated_at' => $newPrice->updated_at->format('d-m-Y H:i'),
                    'location' => $activePrice->location->name ?? 'N/A',
                    'room_type' => $activePrice->roomType->name ?? 'N/A'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui harga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate old active prices when new one is created
     */
    private function deactivateOldPrices($locationId, $roomTypeId, $durationType, $duration)
    {
        ServicePrice::where([
            'location_id' => $locationId,
            'room_type_id' => $roomTypeId,
            'duration_type' => $durationType,
            'duration' => $duration,
            'request_status' => 'active'
        ])
        ->whereNull('parent_id')
        ->update(['request_status' => 'inactive']);
    }
    
    /**
     * API: Get request history
     * Superadmin bisa lihat semua request yang dibuatnya
     */
    public function getMyRequests()
    {
        $user = Auth::user();
        
        $requests = ServicePrice::with([
                'roomType:id,name',
                'location:id,name',
                'location.city:id,name'
            ])
            ->where('requested_by', $user->name)
            ->orderBy('requested_at', 'desc')
            ->paginate(10);
        
        return response()->json([
            'success' => true,
            'requests' => $requests
        ]);
    }

    /**
     * DELETE /api/pricing/{id}/soft-delete
     * Move price to trash (soft delete)
     */
    public function softDeletePrice(Request $request, $priceId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:500'
        ]);
        
        $user = Auth::user();
        
        // Cari tanpa kondisi deleted_at
        $price = ServicePrice::whereIn('request_status', ['rejected', 'inactive'])
            ->findOrFail($priceId);
        
        // Cek jika sudah di-delete
        if ($price->deleted_at && !$price->restored_at) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah dihapus sebelumnya'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Jika data sudah direstore, reset dulu
            if ($price->restored_at) {
                $price->update([
                    'restored_at' => null,
                    'restored_by_user_id' => null,
                    'restore_reason' => null,
                ]);
            }
            
            // ✅ Gunakan delete() dari SoftDeletes trait
            $price->delete();
            
            // ✅ Update field custom DELETE
            $price->update([
                'deleted_by_user_id' => $user->id,
                'deletion_reason' => $validated['reason'],
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dipindahkan ke trash',
                'data' => [
                    'price_id' => $price->id,
                    'deleted_at' => now()->format('Y-m-d H:i'),
                    'reason' => $validated['reason']
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/pricing/trash
     * View all soft deleted prices
     */
    public function getTrash(Request $request)
    {
        $validated = $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'status' => 'in:rejected,inactive,all'
        ]);
        
        $query = ServicePrice::onlyTrashed()
            ->with(['location:id,name', 'roomType:id,name', 'deletedByUser:id,name']);
        
        // Filter by original status
        if ($validated['status'] ?? 'all' !== 'all') {
            $query->where('request_status', $validated['status']);
        }
        
        $prices = $query->orderBy('deleted_at', 'desc')
            ->paginate($validated['per_page'] ?? 20);
        
        return response()->json([
            'success' => true,
            'data' => $prices
        ]);
    }

    /**
     * POST /api/pricing/{id}/restore
     * Restore from trash
     */
    public function restorePrice(Request $request, $priceId)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|min:10|max:500'
        ]);
        
        $user = Auth::user();
        
        $price = ServicePrice::onlyTrashed()->findOrFail($priceId);
        
        DB::beginTransaction();
        
        try {
            // ✅ Gunakan restore() dari SoftDeletes trait
            $price->restore();
            
            // ✅ Update field custom RESTORE
            $price->update([
                'restored_by_user_id' => $user->id,
                'restored_at' => now(),
                'restore_reason' => $validated['reason'] ?? null,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di-restore',
                'data' => [
                    'price_id' => $price->id,
                    'restored_at' => now()->format('Y-m-d H:i'),
                    'deleted_at' => $price->deleted_at ? 'ERROR: Masih ada' : 'NULL (correct)'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal restore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/pricing/{id}/force-delete
     * Permanent delete from trash
     */
    public function forceDeletePrice(Request $request, $priceId)
    {
        try {
            $validated = $request->validate([
                'confirmation' => 'required|string|in:PERMANENT_DELETE'
            ]);
            
            // ⭐ PERBAIKAN: Cari data AKTIF (bukan hanya yang trashed)
            $price = ServicePrice::find($priceId);
            
            // Jika tidak ditemukan, coba cari di trashed juga
            if (!$price) {
                $price = ServicePrice::withTrashed()->find($priceId);
            }
            
            if (!$price) {
                return response()->json([
                    'success' => false,
                    'message' => "Data dengan ID {$priceId} tidak ditemukan",
                    'debug' => [
                        'id' => $priceId,
                        'exists_in_active' => ServicePrice::where('id', $priceId)->exists(),
                        'exists_in_trashed' => ServicePrice::onlyTrashed()->where('id', $priceId)->exists()
                    ]
                ], 404);
            }
            
            \Log::info('Force delete price', [
                'id' => $price->id,
                'status' => $price->request_status,
                'deleted_at' => $price->deleted_at,
                'price' => $price->base_price,
                'user' => auth()->id()
            ]);
            
            // Force delete (permanent)
            $price->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus permanen',
                'data' => [
                    'id' => $priceId,
                    'was_trashed' => !is_null($price->deleted_at)
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Force delete failed', [
                'id' => $priceId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}