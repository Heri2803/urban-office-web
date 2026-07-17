<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Promo;
use App\Models\PromoCategory;
use App\Models\PromoType;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display banner management page
     */
    public function index()
    {
        $roomTypes = \App\Models\RoomType::all();
        return view('layouts.admin.banners-promo', compact('roomTypes'));
    }

    
    /**
     * API endpoint untuk get banners data (untuk Alpine.js)
     * Sekaligus melakukan sinkronisasi status ke DB (fix: tidak hanya in-memory)
     */
    public function apiIndex()
    {
        try {
            // Load SEMUA active promo types dari database
            $promoTypes = PromoType::where('is_active', true)->get();
            
            if ($promoTypes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active promo types found'
                ], 404);
            }

            $promoTypeIds = $promoTypes->pluck('id');

            // Load semua promo berdasarkan active types
            $banners = Promo::with(['category', 'type'])
                        ->whereIn('promo_type_id', $promoTypeIds)
                        ->orderBy('priority', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

            $now = \Carbon\Carbon::now();

            // Kumpulkan ID yang perlu di-update per status
            $toEnded    = [];
            $toUpcoming = [];
            $toActive   = [];
            $toInactive = []; // promo quota habis

            $banners->transform(function ($banner) use ($now, &$toEnded, &$toUpcoming, &$toActive, &$toInactive) {
                if (!in_array($banner->status, ['draft', 'inactive'])) {
                    $start = \Carbon\Carbon::parse($banner->start_date)->startOfDay();
                    $end   = \Carbon\Carbon::parse($banner->end_date)->endOfDay();

                    // Cek QUOTA terlebih dahulu — prioritas di atas cek tanggal
                    // Jika usage_limit terisi dan > 0 dan usage_count sudah mencapai limit,
                    // langsung set inactive tanpa perlu cek tanggal
                    $isQuotaFull = $banner->usage_limit !== null
                                && $banner->usage_limit > 0
                                && (int) $banner->usage_count >= (int) $banner->usage_limit;

                    if ($isQuotaFull) {
                        $newStatus = 'inactive';
                    } elseif ($now->greaterThan($end)) {
                        $newStatus = 'ended';
                    } elseif ($now->lessThan($start)) {
                        $newStatus = 'upcoming';
                    } else {
                        $newStatus = 'active';
                    }

                    // Hanya tandai untuk update DB jika status benar-benar berubah
                    if ($banner->status !== $newStatus) {
                        if ($newStatus === 'ended')    $toEnded[]    = $banner->id;
                        if ($newStatus === 'upcoming') $toUpcoming[] = $banner->id;
                        if ($newStatus === 'active')   $toActive[]   = $banner->id;
                        if ($newStatus === 'inactive') $toInactive[] = $banner->id;
                    }

                    $banner->status = $newStatus;
                }
                return $banner;
            });

            // Bulk update ke DB agar konsisten dengan memory
            if (!empty($toEnded) || !empty($toUpcoming) || !empty($toActive) || !empty($toInactive)) {
                DB::transaction(function () use ($toEnded, $toUpcoming, $toActive, $toInactive, $now) {
                    if (!empty($toEnded)) {
                        Promo::whereIn('id', $toEnded)->update(['status' => 'ended', 'updated_at' => $now]);
                    }
                    if (!empty($toUpcoming)) {
                        Promo::whereIn('id', $toUpcoming)->update(['status' => 'upcoming', 'updated_at' => $now]);
                    }
                    if (!empty($toActive)) {
                        Promo::whereIn('id', $toActive)->update(['status' => 'active', 'updated_at' => $now]);
                    }
                    if (!empty($toInactive)) {
                        Promo::whereIn('id', $toInactive)->update(['status' => 'inactive', 'updated_at' => $now]);
                    }
                });

                $totalChanged = count($toEnded) + count($toUpcoming) + count($toActive) + count($toInactive);
                \Log::info("[BannerController::apiIndex] Bulk status sync: {$totalChanged} promos updated", [
                    'ended'    => count($toEnded),
                    'upcoming' => count($toUpcoming),
                    'active'   => count($toActive),
                    'inactive' => count($toInactive),
                ]);
            }

            \Log::info("API Banners: Found {$banners->count()} promos from " . $promoTypes->count() . " active types");

            return response()->json([
                'success' => true,
                'data'    => $banners
            ]);
            
        } catch (\Exception $e) {
            \Log::error('API Banner Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load banners: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint untuk get categories (untuk Alpine.js)
     */
    public function getCategories()
    {
        try {
            $bannerType = PromoType::where('slug', 'banner')->first();
            
            if (!$bannerType) {
                return response()->json([], 404);
            }

            $categories = PromoCategory::where('promo_type_id', $bannerType->id)
                                      ->where('is_active', true)
                                      ->get(['id', 'name']);

            return response()->json($categories);
            
        } catch (\Exception $e) {
            \Log::error('API Categories Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * API endpoint untuk get service types / room types (untuk Alpine.js)
     */
    public function getServiceTypes()
    {
        try {
            $serviceTypes = \App\Models\RoomType::orderBy('name')->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $serviceTypes
            ]);

        } catch (\Exception $e) {
            \Log::error('API Service Types Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * API endpoint untuk get locations (untuk Alpine.js)
     */
    public function getLocations()
    {
        try {
            $locations = \App\Models\Location::orderBy('name')
                                           ->get(['id', 'name']);

            return response()->json($locations);

        } catch (\Exception $e) {
            \Log::error('API Locations Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * API endpoint untuk get customers (untuk Air-drop Voucher)
     * Bisa di-filter berdasarkan room_type di tabel transactions
     */
    public function getCustomers(Request $request)
    {
        try {
            $query = \App\Models\User::where('role', 'customer');

            // Jika ada filter room_type
            if ($request->filled('room_type')) {
                $roomType = $request->room_type;
                
                // Cek user yang punya transaksi dengan room_type tersebut
                $query->whereHas('transactions', function($q) use ($roomType) {
                    $q->where('room_type', $roomType);
                });
            }

            // Ambil data (id, name, email) untuk dropdown
            $customers = $query->select('id', 'name', 'email')
                               ->orderBy('name', 'asc')
                               ->get();

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
            
        } catch (\Exception $e) {
            \Log::error('API Customers Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        $bannerType = PromoType::where('slug', 'banner')->first();
        $categories = PromoCategory::where('promo_type_id', $bannerType->id)
                                  ->where('is_active', true)
                                  ->get();
        
        $locations = \App\Models\Location::where('is_active', true)->get();
        
        return view('backend.admin.banners.create', compact('categories', 'locations'));
    }

    /**
     * Store a newly created banner
     */
    public function store(StoreBannerRequest $request)
    {
        try {
            $bannerType = PromoType::find($request->promo_type_id);
            
            if (!$bannerType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Promo type not found'
                ], 404);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('banners', 'public');
            }

            $banner = Promo::create([
                'name' => $request->name,
                'code' => $request->code ?: $this->generateBannerCode(),
                'description' => $request->description,
                'promo_type_id' => $request->promo_type_id,
                'promo_category_id' => $request->category_id,
                'status' => $request->status ?? 'draft',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority ?? 1,
                'locations' => $request->locations,
                
                // New fields
                'service_types' => $request->service_types ?? [],
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount ?? 0,
                'min_transaction' => $request->min_transaction ?? 0,
                'usage_limit' => $request->usage_limit,
                'usage_per_user' => $request->usage_per_user ?? 1,
                
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                
                'image_url' => $imagePath,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Jika ini tipe Voucher (id 3) dan ada target_users
            if ($request->promo_type_id == 3 && $request->has('target_users')) {
                // target_users bisa berupa array ID customer
                $targetUsers = is_array($request->target_users) ? $request->target_users : json_decode($request->target_users, true);
                if (!empty($targetUsers)) {
                    // Air-drop: hanya menandai user berhak klaim (is_claimed default false),
                    // BUKAN klaim otomatis. User tetap harus menekan "Claim Offer".
                    $banner->users()->attach($targetUsers);
                    $banner->is_targeted = true;
                    $banner->save();
                }
            }

            // Return full URL untuk image
            $banner->image_url = $imagePath ? '/storage/' . $imagePath : null;
            $banner->load('category', 'type');

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully!',
                'banner' => $banner
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create banner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified banner
     */
    public function show(Promo $banner)
    {
        return view('backend.admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified banner
     */
    public function edit(Promo $banner)
    {
        $bannerType = PromoType::where('slug', 'banner')->first();
        $categories = PromoCategory::where('promo_type_id', $bannerType->id)
                                  ->where('is_active', true)
                                  ->get();
        
        $locations = \App\Models\Location::where('is_active', true)->get();

        return view('backend.admin.banners.edit', compact('banner', 'categories', 'locations'));
    }

    // update data banner
    public function update(UpdateBannerRequest $request, Promo $banner)
    {
        try {
            $imagePath = $banner->image_url;
            
            if ($request->hasFile('image')) {
                if ($banner->image_url) {
                    Storage::disk('public')->delete($banner->image_url);
                }
                $imagePath = $request->file('image')->store('banners', 'public');
            }

            // Update semua field
            $updateData = [
                'name' => $request->name,
                'code' => $request->code ?: $banner->code,
                'description' => $request->description,
                'promo_category_id' => $request->category_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority,
                'locations' => $request->locations,
                'image_url' => $imagePath,
                'updated_by' => auth()->id(),
                
                // Field baru
                'service_types' => $request->service_types ?? [],
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount ?? 0,
                'min_transaction' => $request->min_transaction ?? 0,
                'usage_limit' => $request->usage_limit,
                'usage_per_user' => $request->usage_per_user ?? 1,
                
                'is_approved' => true,
                'approved_at' => $banner->approved_at ?? now(),
                'approved_by' => $banner->approved_by ?? auth()->id(),
            ];

            // Hitung ulang status
            $now = \Carbon\Carbon::now();
            $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            
            $isQuotaFull = $request->usage_limit !== null
                        && $request->usage_limit > 0
                        && (int) $banner->usage_count >= (int) $request->usage_limit;

            $newStatus = $request->status;
            
            // Jika status dari request adalah active/upcoming/ended/inactive, kita bisa mengevaluasi ulang
            // Asumsi: Jika admin memilih 'draft', biarkan draft. Jika tidak, evaluasi ulang.
            if ($newStatus !== 'draft') {
                if ($isQuotaFull) {
                    $newStatus = 'inactive';
                } elseif ($now->greaterThan($end)) {
                    $newStatus = 'ended';
                } elseif ($now->lessThan($start)) {
                    $newStatus = 'upcoming';
                } else {
                    $newStatus = 'active';
                }
            }
            
            $updateData['status'] = $newStatus;

            $banner->update($updateData);

            // Jika ini tipe Voucher (id 3) dan ada target_users
            if ($banner->promo_type_id == 3 && $request->has('target_users')) {
                $targetUsers = is_array($request->target_users) ? $request->target_users : json_decode($request->target_users, true);
                if (is_array($targetUsers)) {
                    // sync() akan hapus target lama yang tidak lagi dipilih, tapi mempertahankan
                    // is_claimed milik user yang tetap ada di daftar (sync tidak reset pivot lain
                    // untuk baris yang tidak berubah relasinya).
                    $banner->users()->sync($targetUsers);
                    $banner->is_targeted = !empty($targetUsers);
                    $banner->save();
                }
            }

            return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                        ->with('error', 'Failed to update banner: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified banner
     */
        public function destroy(Promo $banner)
    {
        try {
            // Delete associated image
            if ($banner->image_url) {
                Storage::disk('public')->delete($banner->image_url);
            }

            $banner->delete();

            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Banner deleted successfully!'
                ]);
            }

            return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner deleted successfully!');

        } catch (\Exception $e) {
            // Check if it's an AJAX request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete banner: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                        ->with('error', 'Failed to delete banner: ' . $e->getMessage());
        }
    }

    /**
     * Toggle banner status
     */
    public function toggleStatus(Promo $banner)
    {
        try {
            $banner->update([
                'status' => $banner->status === 'active' ? 'inactive' : 'active',
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner status updated!',
                'new_status' => $banner->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique banner code
     */
    private function generateBannerCode(): string
    {
        do {
            $code = 'URB' . strtoupper(substr(uniqid(), -6));
        } while (Promo::where('code', $code)->exists());

        return $code;
    }

    /**
     * API endpoint untuk get promo types
     */
    public function getPromoTypes()
    {
        try {
            $types = PromoType::where('is_active', true)->get(['id', 'name', 'slug']);
            return response()->json($types);
        } catch (\Exception $e) {
            \Log::error('API Promo Types Error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}