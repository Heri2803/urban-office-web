<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Promo;
use App\Models\PromoCategory;
use App\Models\PromoType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display banner management page
     */
    public function index()
    {
        return view('layouts.admin.banners-promo');
    }

    
    /**
     * API endpoint untuk get banners data (untuk Alpine.js)
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

            \Log::info("API Banners: Found {$banners->count()} promos from " . $promoTypes->count() . " active types");

            return response()->json([
                'success' => true,
                'data' => $banners
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
     * API endpoint untuk get locations (untuk Alpine.js)
     */
    public function getLocations()
    {
        try {
            $locations = \App\Models\Location::where('is_active', true)
                                           ->get(['id', 'name', 'code']);

            return response()->json($locations);
            
        } catch (\Exception $e) {
            \Log::error('API Locations Error: ' . $e->getMessage());
            return response()->json([], 500);
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
                'code' => $this->generateBannerCode(),
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
                
                'image_url' => $imagePath,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

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
                'description' => $request->description,
                'promo_category_id' => $request->category_id,
                'status' => $request->status,
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
            ];

            $banner->update($updateData);

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
            $code = 'BNR' . strtoupper(substr(uniqid(), -6));
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