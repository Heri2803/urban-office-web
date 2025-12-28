<?php

namespace App\Http\Controllers\backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\ServicePrice;
use App\Models\Room;
use App\Models\RoomType;

class AdminServicePriceController extends Controller
{
    /**
     * Dashboard statistics
     */
    public function dashboardStats()
    {
        try {
            $stats = [
                'total_categories' => ServiceCategory::count(),
                'total_default_prices' => ServicePrice::whereNull('room_id')
                    ->whereNull('room_type_id')
                    ->where('request_status', 'active')
                    ->count(),
                'pending_requests' => ServicePrice::where('request_status', 'pending')->count(),
                'room_specific_prices' => ServicePrice::where(function($query) {
                    $query->whereNotNull('room_id')
                          ->orWhereNotNull('room_type_id');
                })->where('request_status', 'active')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    
    /**
     * Get all DEFAULT prices (global) grouped by category
     */
    public function getDefaultPrices()
    {
        try {
            // Get ALL service prices (active)
            $allPrices = ServicePrice::with(['room', 'roomType', 'category'])
                ->where('request_status', 'active')
                ->orderBy('service_category_id')
                ->orderBy('room_id')
                ->orderBy('room_type_id')
                ->orderBy('duration_type')
                ->orderBy('duration')
                ->get();
            
            // Group by service_category_id
            $groupedByCategory = $allPrices->groupBy('service_category_id');
            
            $formatted = [];
            
            foreach ($groupedByCategory as $categoryId => $prices) {
                // Get category name
                $category = ServiceCategory::find($categoryId);
                
                // If category not found, create placeholder
                if (!$category) {
                    $categoryName = $this->getCategoryNameFromPrices($prices);
                    $categorySlug = $this->createSlug($categoryName);
                    $categoryIcon = $this->getIconForCategory($categoryName);
                } else {
                    $categoryName = $category->name;
                    $categorySlug = $this->createSlug($category->name);
                    $categoryIcon = $this->getIconForCategory($category->name);
                }
                
                // Group prices by type
                $roomSpecific = $prices->filter(function($price) {
                    return !is_null($price->room_id);
                })->groupBy('room_id');
                
                $roomTypeSpecific = $prices->filter(function($price) {
                    return is_null($price->room_id) && !is_null($price->room_type_id);
                })->groupBy('room_type_id');
                
                $global = $prices->filter(function($price) {
                    return is_null($price->room_id) && is_null($price->room_type_id);
                });
                
                // Format room-specific prices
                $formattedRoomSpecific = [];
                foreach ($roomSpecific as $roomId => $roomPrices) {
                    $room = \App\Models\Room::find($roomId);
                    $formattedRoomSpecific[] = [
                        'room' => $room ? [
                            'id' => $room->id,
                            'name' => $room->name,
                            'room_type_id' => $room->room_type_id
                        ] : null,
                        'prices' => $roomPrices->map(function($price) {
                            return $this->formatPriceData($price);
                        })
                    ];
                }
                
                // Format room-type specific prices
                $formattedRoomTypeSpecific = [];
                foreach ($roomTypeSpecific as $roomTypeId => $typePrices) {
                    $roomType = \App\Models\RoomType::find($roomTypeId);
                    $formattedRoomTypeSpecific[] = [
                        'room_type' => $roomType ? [
                            'id' => $roomType->id,
                            'name' => $roomType->name
                        ] : null,
                        'prices' => $typePrices->map(function($price) {
                            return $this->formatPriceData($price);
                        })
                    ];
                }
                
                $formatted[] = [
                    'id' => $categoryId,
                    'name' => $categoryName,
                    'slug' => $categorySlug,
                    'icon' => $categoryIcon,
                    'category_exists' => !is_null($category),
                    'prices' => [
                        'room_specific' => $formattedRoomSpecific,
                        'room_type_specific' => $formattedRoomTypeSpecific,
                        'global' => $global->map(function($price) {
                            return $this->formatPriceData($price);
                        }),
                    ],
                    'stats' => [
                        'total_prices' => $prices->count(),
                        'room_specific_count' => $roomSpecific->count(),
                        'room_type_specific_count' => $roomTypeSpecific->count(),
                        'global_count' => $global->count(),
                    ]
                ];
            }
            
            // Also include categories without prices
            $categoriesWithoutPrices = ServiceCategory::whereNotIn('id', array_keys($groupedByCategory->toArray()))->get();
            
            foreach ($categoriesWithoutPrices as $category) {
                $formatted[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $this->createSlug($category->name),
                    'icon' => $this->getIconForCategory($category->name),
                    'category_exists' => true,
                    'prices' => [
                        'room_specific' => [],
                        'room_type_specific' => [],
                        'global' => [],
                    ],
                    'stats' => [
                        'total_prices' => 0,
                        'room_specific_count' => 0,
                        'room_type_specific_count' => 0,
                        'global_count' => 0,
                    ],
                    'note' => 'No pricing data found'
                ];
            }
            
            // Sort by name
            usort($formatted, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            
            return response()->json([
                'success' => true,
                'data' => $formatted,
                'summary' => [
                    'total_categories' => count($formatted),
                    'categories_with_prices' => count(array_filter($formatted, function($cat) {
                        return $cat['stats']['total_prices'] > 0;
                    })),
                    'total_price_records' => array_sum(array_column(array_column($formatted, 'stats'), 'total_prices')),
                    'categories_missing_in_db' => count(array_filter($formatted, function($cat) {
                        return !$cat['category_exists'];
                    })),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
    
    /**
     * Get DEFAULT prices for a specific category
     */
        public function getCategoryDefaultPrices($categoryName)
    {
        try {
            $categoryName = urldecode(str_replace('-', ' ', $categoryName));
            
            $category = ServiceCategory::where('name', 'like', '%' . $categoryName . '%')->first();
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service category not found',
                    'searched_for' => $categoryName,
                    'available_categories' => ServiceCategory::pluck('name')->toArray()
                ], 404);
            }
            
            // Get ALL active prices for this category (grouped by room_type)
            $prices = ServicePrice::with(['roomType'])
                ->where('service_category_id', $category->id)
                ->where('request_status', 'active')
                ->whereNull('room_id') // Exclude specific room prices
                ->orderBy('room_type_id')
                ->orderBy('duration_type')
                ->orderBy('duration')
                ->get();
            
            // Group by room_type
            $grouped = $prices->groupBy(function($price) {
                return $price->room_type_id ?: 'global';
            })->map(function($group, $roomTypeId) {
                $roomType = $roomTypeId !== 'global' 
                    ? RoomType::find($roomTypeId)
                    : null;
                
                return [
                    'room_type' => $roomType ? [
                        'id' => $roomType->id,
                        'name' => $roomType->name
                    ] : null,
                    'is_global' => $roomTypeId === 'global',
                    'prices' => $group->map(function($price) {
                        return $this->formatPriceData($price);
                    }),
                    'count' => $group->count()
                ];
            });
            
            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $this->createSlug($category->name)
                ],
                'prices_by_room_type' => $grouped,
                'total_price_variants' => $prices->count(),
                'room_type_count' => $grouped->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Submit DEFAULT price change request
     */
    public function requestDefaultPriceChange(Request $request)
    {
        \Log::info('Price Change Request from User:', [
            'user_id' => auth()->id(),
            'user_location' => auth()->user()->location_id,
            'data' => $request->all()
        ]);
        
        // ✅ VALIDATION (tanpa location_id - akan diambil dari user)
        $validated = $request->validate([
            'price_id' => 'required_if:apply_to_all,false|exists:service_prices,id',
            'proposed_price' => 'required|numeric|min:1000',
            'reason' => 'required|string|max:500',
            'coffee_break_price' => 'nullable|numeric',
            'deposit' => 'nullable|numeric',
            'apply_to_all' => 'required|boolean',
            'duration_type' => 'required_if:apply_to_all,true|in:hour,day,week,month,year',
            'duration' => 'required_if:apply_to_all,true|integer|min:1',
            'room_type_id' => 'required_if:apply_to_all,true|exists:room_types,id',
            'category_id' => 'nullable'
        ]);

        try {
            \DB::beginTransaction();

            // ✅ GET USER'S LOCATION
            $userLocationId = auth()->user()->location_id;
            
            // Validasi: User harus punya location
            if (!$userLocationId) {
                \Log::warning('User without location tried to submit price change', [
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'User does not have an assigned location. Please contact admin.'
                ], 403);
            }

            if ($request->boolean('apply_to_all')) {
                // ✅ BULK MODE - Untuk location user TAPI juga terima legacy data (NULL location)
                $categoryId = $this->normalizeCategoryId($request->category_id);
                
                \Log::info('BULK MODE - Query Criteria:', [
                    'duration_type' => $validated['duration_type'],
                    'duration' => $validated['duration'],
                    'room_type_id' => $validated['room_type_id'],
                    'user_location' => $userLocationId,
                    'category_id' => $categoryId
                ]);
                
                // Build query untuk mencari parent prices
                $query = ServicePrice::where([
                    'duration_type' => $validated['duration_type'],
                    'duration' => $validated['duration'],
                    'room_type_id' => $validated['room_type_id'],
                    'request_status' => 'active',
                ])->whereNull('parent_id'); // Hanya parent prices (bukan requests)
                
                // ✅ CRITICAL FIX: Cari parent prices dengan location user ATAU NULL (legacy data)
                $query->where(function($q) use ($userLocationId) {
                    $q->where('location_id', $userLocationId)   // Exact match user's location
                    ->orWhereNull('location_id');              // OR legacy prices (no location yet)
                });
                
                // Filter by category
                if ($categoryId === null) {
                    $query->whereNull('service_category_id');
                    \Log::info('Filtering: category_id IS NULL');
                } else {
                    $query->where('service_category_id', $categoryId);
                    \Log::info('Filtering: category_id = ' . $categoryId);
                }
                
                $allPrices = $query->get();
                
                \Log::info('BULK MODE - Found parent prices:', [
                    'count' => $allPrices->count(),
                    'locations_found' => $allPrices->pluck('location_id')->unique()->values(),
                    'sample_ids' => $allPrices->take(3)->pluck('id')
                ]);
                
                if ($allPrices->isEmpty()) {
                    \Log::warning('No parent prices found for bulk request', [
                        'criteria' => [
                            'duration_type' => $validated['duration_type'],
                            'duration' => $validated['duration'],
                            'room_type_id' => $validated['room_type_id'],
                            'user_location' => $userLocationId,
                            'category_null' => ($categoryId === null)
                        ]
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'error' => 'No prices found with the specified criteria'
                    ], 404);
                }
                
                // ✅ Create price change requests untuk setiap parent price
                $createdCount = 0;
                $createdRequests = [];
                
                foreach ($allPrices as $parentPrice) {
                    $requestData = $this->prepareRequestData($parentPrice, $validated);
                    
                    // ✅ NEW requests SELALU dapat location_id dari user
                    $requestData['location_id'] = $userLocationId;
                    
                    $priceRequest = ServicePrice::create($requestData);
                    $createdRequests[] = $priceRequest->id;
                    $createdCount++;
                }
                
                \DB::commit();
                
                \Log::info('BULK MODE - Requests created successfully', [
                    'count' => $createdCount,
                    'request_ids' => $createdRequests,
                    'user_location_id' => $userLocationId
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => "Price change submitted for {$createdCount} room(s)",
                    'rooms_affected' => $createdCount,
                    'user_location_id' => $userLocationId,
                    'request_count' => $createdCount
                ]);
            }

            // ✅ SINGLE MODE
            \Log::info('SINGLE MODE - Processing price_id: ' . $validated['price_id']);
            
            $parentPrice = ServicePrice::findOrFail($validated['price_id']);
            
            \Log::info('SINGLE MODE - Parent price details:', [
                'parent_id' => $parentPrice->id,
                'parent_location_id' => $parentPrice->location_id,
                'user_location_id' => $userLocationId,
                'room_type_id' => $parentPrice->room_type_id,
                'service_category_id' => $parentPrice->service_category_id
            ]);
            
            // ✅ Validasi: Parent price harus di location user ATAU NULL (legacy)
            // Hanya reject jika parent punya location_id spesifik DAN berbeda dengan user
            if ($parentPrice->location_id && $parentPrice->location_id != $userLocationId) {
                \Log::warning('Location mismatch in single mode', [
                    'parent_location' => $parentPrice->location_id,
                    'user_location' => $userLocationId,
                    'parent_id' => $parentPrice->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot modify price from different location'
                ], 403);
            }
            
            // Jika parent location_id = NULL, tetap boleh (legacy data)
            $requestData = $this->prepareRequestData($parentPrice, $validated);
            
            // ✅ NEW request SELALU dapat location_id dari user
            $requestData['location_id'] = $userLocationId;
            
            $priceRequest = ServicePrice::create($requestData);
            
            \DB::commit();
            
            \Log::info('SINGLE MODE - Request created successfully', [
                'request_id' => $priceRequest->id,
                'parent_id' => $parentPrice->id,
                'user_location_id' => $userLocationId
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Price change request submitted successfully',
                'data' => [
                    'id' => $priceRequest->id,
                    'parent_id' => $priceRequest->parent_id,
                    'location_id' => $priceRequest->location_id,
                    'proposed_price' => $priceRequest->base_price,
                    'status' => $priceRequest->request_status
                ],
                'user_location_id' => $userLocationId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed in price change request', [
                'errors' => $e->errors(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \DB::rollBack();
            \Log::error('Model not found in price change request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Price not found'
            ], 404);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Price Change Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing price request
     */
    public function updateRequest(Request $request, $id)
    {
        // ✅ DEBUG LOG
        \Log::info('=== UPDATE REQUEST CALLED ===', [
            'request_id' => $id,
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user_object' => auth()->user(),
            'has_cookie' => $request->hasCookie('urbanoffice_session'),
            'ip' => $request->ip()
        ]);
        
        \DB::beginTransaction();
        
        try {
            // ✅ SAFE USER NAME GETTER
            $userName = 'System';
            if (auth()->check() && $user = auth()->user()) {
                $userName = $user->name ?? $user->email ?? 'User#' . $user->id;
                \Log::info('User identified', ['user' => $userName]);
            } else {
                \Log::warning('No authenticated user for update request');
            }
            
            // ✅ VALIDATION
            $validated = $request->validate([
                'proposed_price' => 'required|numeric|min:1000',
                'reason' => 'required|string|max:500'
            ]);
            
            // ✅ FIND REQUEST
            $priceRequest = ServicePrice::find($id);
            
            if (!$priceRequest) {
                \Log::warning('Update failed: Request not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Price request not found'
                ], 404);
            }
            
            // ✅ STATUS CHECK
            if ($priceRequest->request_status !== 'pending') {
                \Log::warning('Update failed: Wrong status', [
                    'id' => $id,
                    'current_status' => $priceRequest->request_status
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Only pending requests can be edited. Current status: ' . $priceRequest->request_status
                ], 400);
            }
            
            // ✅ PERMISSION CHECK (optional)
            // Jika ingin cek apakah user adalah pembuat request
            if ($priceRequest->requested_by && $priceRequest->requested_by !== $userName) {
                \Log::warning('Update failed: Permission denied', [
                    'creator' => $priceRequest->requested_by,
                    'current_user' => $userName
                ]);
                // return response()->json(['error' => 'You can only edit your own requests'], 403);
            }
            
            // ✅ STORE OLD VALUES
            $oldPrice = $priceRequest->base_price;
            $oldReason = $priceRequest->request_reason;
            
            // ✅ UPDATE
            $priceRequest->update([
                'base_price' => $validated['proposed_price'],
                'request_reason' => $validated['reason'],
                'updated_at' => now()
                // Jika ada kolom 'updated_by' di tabel:
                // 'updated_by' => $userName,
            ]);
            
            \DB::commit();
            
            \Log::info('Update successful', [
                'request_id' => $id,
                'user' => $userName,
                'old_price' => $oldPrice,
                'new_price' => $validated['proposed_price'],
                'price_change' => $validated['proposed_price'] - $oldPrice
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Price request updated successfully',
                'data' => [
                    'id' => $priceRequest->id,
                    'old_price' => (float) $oldPrice,
                    'new_price' => (float) $priceRequest->base_price,
                    'price_change' => (float) ($priceRequest->base_price - $oldPrice),
                    'percentage_change' => $oldPrice > 0 ? 
                        round((($priceRequest->base_price - $oldPrice) / $oldPrice) * 100, 2) : null,
                    'reason' => $priceRequest->request_reason,
                    'updated_by' => $userName,
                    'updated_at' => $priceRequest->updated_at->format('Y-m-d H:i:s'),
                    'status' => $priceRequest->request_status
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed in update', [
                'errors' => $e->errors(),
                'request_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Update failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to update request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a price request
     */
    public function deleteRequest($id)
    {
        \Log::info('=== DELETE REQUEST CALLED ===', [
            'request_id' => $id,
            'user_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'ip' => request()->ip()
        ]);
        
        try {
            // ✅ SAFE USER NAME GETTER
            $userName = 'System';
            if (auth()->check() && $user = auth()->user()) {
                $userName = $user->name ?? $user->email ?? 'User#' . $user->id;
                \Log::info('User identified for delete', ['user' => $userName]);
            } else {
                \Log::warning('No authenticated user for delete request');
            }
            
            \DB::beginTransaction();
            
            // ✅ FIND REQUEST
            $priceRequest = ServicePrice::find($id);
            
            if (!$priceRequest) {
                \Log::warning('Delete failed: Request not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error' => 'Price request not found'
                ], 404);
            }
            
            // ✅ BUSINESS RULE VALIDATION
            $allowedStatuses = ['pending', 'inactive'];
            if (!in_array($priceRequest->request_status, $allowedStatuses)) {
                \Log::warning('Delete failed: Invalid status', [
                    'id' => $id,
                    'current_status' => $priceRequest->request_status,
                    'allowed_statuses' => $allowedStatuses
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot delete request with status: ' . $priceRequest->request_status .
                            '. Only pending or inactive requests can be deleted.'
                ], 400);
            }
            
            // ✅ CHECK IF ACTIVE PRICE HAS CHILD REQUESTS
            if ($priceRequest->request_status === 'active') {
                $hasChildRequests = ServicePrice::where('parent_id', $id)->exists();
                
                if ($hasChildRequests) {
                    \Log::warning('Cannot delete active price with child requests', [
                        'request_id' => $id,
                        'child_count' => ServicePrice::where('parent_id', $id)->count()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'error' => 'Cannot delete active price that has pending requests'
                    ], 400);
                }
            }
            
            // ✅ STORE DATA FOR LOGGING BEFORE DELETION
            $deletedData = [
                'id' => $priceRequest->id,
                'parent_id' => $priceRequest->parent_id,
                'base_price' => $priceRequest->base_price,
                'request_status' => $priceRequest->request_status,
                'requested_by' => $priceRequest->requested_by,
                'deleted_by' => $userName,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ];
            
            \Log::info('Deleting request', $deletedData);
            
            // ✅ DELETE
            $priceRequest->delete();
            
            \DB::commit();
            
            \Log::info('Request deleted successfully', ['request_id' => $id]);
            
            // ✅ AUDIT LOG (optional)
            if (class_exists('\App\Models\AuditLog')) {
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'delete_price_request',
                    'model_type' => ServicePrice::class,
                    'model_id' => $id,
                    'old_values' => json_encode($deletedData),
                    'new_values' => null,
                    'ip_address' => request()->ip()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Price request deleted successfully',
                'data' => [
                    'deleted_id' => $id,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                    'deleted_by' => $userName,
                    'status' => $priceRequest->request_status
                ]
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Log::error('Delete failed: ' . $e->getMessage(), [
                'request_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete request: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ SINGLE HELPER METHOD
    private function normalizeCategoryId($categoryId)
    {
        if ($categoryId === null || $categoryId === 'null') {
            return null;
        }
        
        if (is_string($categoryId) && str_starts_with($categoryId, 'null-')) {
            return null; // "null-xxx" becomes null
        }
        
        return is_numeric($categoryId) ? (int)$categoryId : $categoryId;
    }

    // ✅ SINGLE DATA PREP METHOD
    private function prepareRequestData($parentPrice, $validated)
    {
        $user = auth()->user();
        
        // ✅ DEBUG: Cek user location
        \Log::info('prepareRequestData Debug:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_location_id' => $user->location_id, // ← CEK INI
            'has_location' => !empty($user->location_id)
        ]);
        
        return [
            'parent_id' => $parentPrice->id,
            'room_id' => $parentPrice->room_id,
            'room_type_id' => $parentPrice->room_type_id,
            'service_category_id' => $parentPrice->service_category_id,
            'duration_type' => $parentPrice->duration_type,
            'duration' => $parentPrice->duration,
            'base_price' => $validated['proposed_price'],
            'coffee_break_option' => $parentPrice->coffee_break_option,
            'coffee_break_price' => $validated['coffee_break_price'] ?? $parentPrice->coffee_break_price,
            'deposit' => $validated['deposit'] ?? $parentPrice->deposit,
            'previous_price' => $parentPrice->base_price,
            'request_status' => 'pending',
            'request_reason' => $validated['reason'],
            'requested_by' => $user->name,
            'requested_at' => now(),
            'location_id' => $user->location_id, // ← JIKA INI NULL, MASALAH DI SINI
        ];
    }
    
    /**
     * Get all pending price requests
     */
    public function getPendingRequests()
    {
        try {
            $requests = ServicePrice::with(['category', 'room', 'roomType', 'parent'])
                ->where('request_status', 'pending')
                ->orderBy('requested_at', 'desc')
                ->get()
                ->map(function($request) {
                    return [
                        'id' => $request->id,
                        'parent_id' => $request->parent_id,
                        'room_type' => $request->roomType ? [
                            'id' => $request->roomType->id,
                            'name' => $request->roomType->name
                        ] : null,
                        'category' => $request->category ? [
                            'id' => $request->category->id,
                            'name' => $request->category->name
                        ] : null,
                        'room' => $request->room ? [
                            'id' => $request->room->id,
                            'name' => $request->room->name
                        ] : null,
                        'duration_display' => $this->formatDuration($request),
                        'current_price' => (float) $request->previous_price,
                        'proposed_price' => (float) $request->base_price,
                        'percentage_change' => $this->calculatePercentageChange(
                            $request->previous_price,
                            $request->base_price
                        ),
                        'reason' => $request->request_reason,
                        'requested_by' => $request->requested_by,
                        'requested_at' => $request->requested_at,
                        'has_coffee_break' => !empty($request->coffee_break_option),
                        'coffee_break_option' => $request->coffee_break_option,
                        'coffee_break_price' => $request->coffee_break_price ? (float) $request->coffee_break_price : null,
                        'deposit' => $request->deposit ? (float) $request->deposit : null,
                    ];
                });
                
            return response()->json([
                'success' => true,
                'data' => $requests,
                'count' => $requests->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Approve a price request
     */
    public function approveRequest($id)
    {
        \DB::beginTransaction();
        
        try {
            $priceRequest = ServicePrice::find($id);
            
            if (!$priceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Price request not found'
                ], 404);
            }
            
            if ($priceRequest->request_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be approved'
                ], 400);
            }
            
            // Update the parent (current active) price to inactive
            if ($priceRequest->parent_id) {
                $parentPrice = ServicePrice::find($priceRequest->parent_id);
                if ($parentPrice) {
                    $parentPrice->update(['request_status' => 'inactive']);
                }
            }
            
            // Update the request to active (now becomes the current price)
            $priceRequest->update([
                'request_status' => 'active',
                'reviewed_by' => auth()->check() ? auth()->user()->name : 'Super Admin',
                'reviewed_at' => now(),
                'parent_id' => null, // This is now the main price
            ]);
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Price request approved successfully',
                'data' => [
                    'id' => $priceRequest->id,
                    'new_price' => (float) $priceRequest->base_price,
                    'effective_date' => now()->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject a price request
     */
    public function rejectRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $priceRequest = ServicePrice::where('id', $id)
            ->where('request_status', 'pending')
            ->firstOrFail();
        
        $priceRequest->update([
            'request_status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'reviewed_by' => auth()->user()->name ?? 'Admin',
            'reviewed_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Request rejected',
            'data' => $priceRequest
        ]);
    }

    public function getRequestHistory()
    {
        try {
            $history = ServicePrice::with(['category', 'room', 'roomType'])
                ->whereIn('request_status', ['rejected', 'inactive', 'active', 'pending'])
                ->orderBy('requested_at', 'desc')
                ->get()
                ->map(function($record) {
                    // ✅ TAMBAHKAN ROOM INFORMATION
                    $roomData = null;
                    if ($record->room) {
                        $roomData = [
                            'room_id' => $record->room->id,
                            'room_number' => $record->room->room_number,
                            'floor' => $record->room->floor,
                            'capacity' => $record->room->capacity,
                        ];
                    }
                    
                    return [
                        'id' => $record->id,
                        'room_type' => $record->roomType ? $record->roomType->name : 'N/A',
                        'room_type_id' => $record->room_type_id, // ✅ TAMBAHKAN
                        'category' => $record->category ? $record->category->name : 'General',
                        'category_id' => $record->category_id, // ✅ TAMBAHKAN
                        'duration_display' => $this->formatDuration($record),
                        'duration_type' => $record->duration_type, // ✅ TAMBAHKAN
                        'duration' => $record->duration, // ✅ TAMBAHKAN
                        'price' => (float) $record->base_price,
                        'previous_price' => (float) $record->previous_price,
                        'status' => $record->request_status,
                        'reason' => $record->request_reason,
                        'requested_by' => $record->requested_by,
                        'requested_at' => $record->requested_at,
                        'reviewed_by' => $record->reviewed_by,
                        'reviewed_at' => $record->reviewed_at,
                        
                        // ✅ ROOM DATA (jika ada)
                        'room' => $roomData,
                        'room_id' => $record->room_id, // Direct room_id
                        
                        // ✅ ADDITIONAL FIELDS
                        'coffee_break_option' => $record->coffee_break_option,
                        'coffee_break_price' => $record->coffee_break_price,
                        'deposit' => $record->deposit,
                        'apply_to_all' => $record->apply_to_all ?? false,
                        'location_id' => $record->location_id,
                    ];
                });
                
            return response()->json([
                'success' => true,
                'data' => $history,
                'count' => $history->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getServiceCategories()
    {
        try {
            \Log::info('Fetching service categories for pricing page');
            
            // OPTION A: Ambil dari tabel service_categories
            $categories = ServiceCategory::select('id', 'name', 'description')
                ->orderBy('name')
                ->get();
            
            // OPTION B: Ambil categories yang punya active prices
            $categoriesWithPrices = ServicePrice::select('service_category_id')
                ->where('request_status', 'active')
                ->whereNotNull('service_category_id')
                ->distinct()
                ->get()
                ->pluck('service_category_id');
            
            // Gabungkan kedua sumber
            $categories = ServiceCategory::whereIn('id', $categoriesWithPrices)
                ->orWhere(function($query) use ($categoriesWithPrices) {
                    // Include categories without prices but might be needed
                    if ($categoriesWithPrices->isEmpty()) {
                        $query->whereNotNull('id'); // Get all if no prices yet
                    }
                })
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'count' => $categories->count(),
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getServiceCategories: ' . $e->getMessage());
            
            // JANGAN hardcode! Return error dengan data kosong
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [], // Array kosong lebih baik
                'count' => 0
            ], 500);
        }
    }

    
    // ========== HELPER METHODS ==========
    
    /**
     * Create slug from category name
     */
    private function createSlug($name)
    {
        return strtolower(str_replace(' ', '-', $name));
    }
    
    /**
     * Get icon for category
     */
    private function getIconForCategory($categoryName)
    {
        $icons = [
            'Meeting' => '🏢',
            'Private' => '🚪',
            'Sharing' => '👥',
            'Virtual' => '💼',
            'Coworking' => '🖥️',
            'Event' => '🎉',
        ];
        
        foreach ($icons as $key => $icon) {
            if (stripos($categoryName, $key) !== false) {
                return $icon;
            }
        }
        
        return '💰';
    }
    
    /**
     * Format duration for display
     */
    private function formatDuration($price)
    {
        if ($price->duration_type === 'hour') {
            return $price->duration == 1 ? '1 Hour' : "{$price->duration} Hours";
        }
        if ($price->duration_type === 'day') {
            if ($price->duration == 7) return '1 Week';
            if ($price->duration == 30) return '1 Month';
            return "{$price->duration} Days";
        }
        return "{$price->duration} " . ucfirst($price->duration_type);
    }

        private function formatPriceData($price)
{
    $data = [
        'id' => $price->id,
        'duration_type' => $price->duration_type,
        'duration' => $price->duration,
        'duration_display' => $this->formatDuration($price),
        'base_price' => (float) $price->base_price,
        'request_status' => $price->request_status,
        'has_coffee_break' => !empty($price->coffee_break_option),
    ];
    
    // Coffee break information
    if (!empty($price->coffee_break_option)) {
        $coffeeOption = $price->coffee_break_option;
        $data['coffee_break_option'] = $coffeeOption;
        
        // Parse coffee break count (handle both "1" and "1x" formats)
        if (is_numeric($coffeeOption)) {
            $coffeeCount = (int) $coffeeOption;
            $data['coffee_break_count'] = $coffeeCount;
            $data['coffee_break_display'] = $coffeeCount . 'x';
        } elseif (preg_match('/(\d+)x?/i', $coffeeOption, $matches)) {
            $coffeeCount = (int) $matches[1];
            $data['coffee_break_count'] = $coffeeCount;
            $data['coffee_break_display'] = $coffeeCount . 'x';
        } else {
            $data['coffee_break_display'] = $coffeeOption;
        }
        
        if ($price->coffee_break_price) {
            $data['coffee_break_price'] = (float) $price->coffee_break_price;
            
            // Calculate total with coffee break
            $coffeeCount = $data['coffee_break_count'] ?? 1;
            $data['total_with_coffee_break'] = (float) $price->base_price + 
                ((float) $price->coffee_break_price * $coffeeCount);
        }
    }
    
    // Deposit
    if (!is_null($price->deposit) && (float) $price->deposit > 0) {
        $data['deposit'] = (float) $price->deposit;
    }
    
    // Room/Room Type info
    if ($price->room_id) {
        $data['room_id'] = $price->room_id;
    }
    
    if ($price->room_type_id) {
        $data['room_type_id'] = $price->room_type_id;
    }
    
    // Parent info (for requests)
    if ($price->parent_id) {
        $data['parent_id'] = $price->parent_id;
    }
    
    return $data;
}

    /**
     * Get suggested room type for a service category
     */
    private function getSuggestedRoomType($categoryName)
    {
        $mapping = [
            'small meeting' => 2, // Meeting Room
            'big meeting' => 2,   // Meeting Room
            'office' => 1,        // Private Office
            'executive' => 1,     // Private Office
            'premiere' => 1,      // Private Office
            'empire' => 1,        // Private Office
            'daily pass' => 4,    // Coworking Space
            'student pass' => 4,  // Coworking Space
            'membership coworking space' => 4, // Coworking Space
        ];
        
        $lowerName = strtolower($categoryName);
        
        foreach ($mapping as $key => $roomTypeId) {
            if (str_contains($lowerName, $key)) {
                return $roomTypeId;
            }
        }
        
        return null;
    }

        private function getCategoryNameFromPrices($prices)
    {
        // Try to determine from room_type_id
        $roomTypeIds = $prices->pluck('room_type_id')->unique()->filter()->values();
        
        if ($roomTypeIds->count() > 0) {
            $roomType = \App\Models\RoomType::find($roomTypeIds->first());
            if ($roomType) {
                return $roomType->name . ' Package';
            }
        }
        
        // Try from service_category_id mapping
        $categoryId = $prices->first()->service_category_id;
        
        $mapping = [
            6 => 'Event Space',
            7 => 'Coworking Daily Pass (Normal)',
            8 => 'Coworking Daily Pass (Student)',
            9 => 'Coworking Membership',
        ];
        
        return $mapping[$categoryId] ?? 'Unknown Category ' . $categoryId;
    }

    public function getPricesByRoomType()
{
    try {
        $roomTypes = RoomType::all();
        
        // Pre-load rooms dengan hanya kolom yang diperlukan
        $rooms = \App\Models\Room::select('id', 'room_number', 'floor', 'location_id')
            ->get()
            ->keyBy('id');
        
        $formatted = $roomTypes->map(function($roomType) use ($rooms) {
            $prices = ServicePrice::with(['category'])
                ->where('room_type_id', $roomType->id)
                ->where('request_status', 'active')
                ->orderBy('service_category_id')
                ->orderBy('room_id')
                ->orderBy('duration_type')
                ->orderBy('duration')
                ->get();
            
            $groupedByCategory = $prices->groupBy(function($price) {
                return $price->service_category_id ?: 'no_category';
            });
            
            $categories = [];
            foreach ($groupedByCategory as $categoryId => $categoryPrices) {
                if ($categoryId === 'no_category') {
                    $categoryName = 'General Pricing';
                    $categorySlug = 'general';
                } else {
                    $category = ServiceCategory::find($categoryId);
                    $categoryName = $category ? $category->name : 'Unknown Category';
                    $categorySlug = $category ? $this->createSlug($category->name) : 'unknown';
                }
                
                $roomSpecific = $categoryPrices->filter(function($price) {
                    return !is_null($price->room_id);
                })->groupBy('room_id');
                
                $general = $categoryPrices->filter(function($price) {
                    return is_null($price->room_id);
                });
                
                $formattedRoomSpecific = [];
                foreach ($roomSpecific as $roomId => $roomPrices) {
                    $room = $rooms[$roomId] ?? null;
                    
                    $formattedRoomSpecific[] = [
                        'room' => $room ? [
                            'id' => $room->id,
                            'name' => $this->formatRoomName($room),
                            'room_number' => $room->room_number,
                            'floor' => $room->floor
                        ] : [
                            'id' => $roomId,
                            'name' => 'Room #' . $roomId,
                            'room_number' => (string)$roomId
                        ],
                        'prices' => $roomPrices->map(function($price) {
                            return $this->formatPriceData($price);
                        })
                    ];
                }
                
                $categories[] = [
                    'category_id' => $categoryId !== 'no_category' ? (int)$categoryId : null,
                    'category_name' => $categoryName,
                    'category_slug' => $categorySlug,
                    'prices' => [
                        'room_specific' => $formattedRoomSpecific,
                        'general' => $general->map(function($price) {
                            return $this->formatPriceData($price);
                        }),
                    ],
                    'stats' => [
                        'total' => $categoryPrices->count(),
                        'room_specific' => $roomSpecific->count(),
                        'general' => $general->count(),
                    ]
                ];
            }
            
            return [
                'room_type' => [
                    'id' => $roomType->id,
                    'name' => $roomType->name,
                ],
                'categories' => $categories,
                'stats' => [
                    'total_prices' => $prices->count(),
                    'total_categories' => count($categories),
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formatted,
            'summary' => [
                'total_room_types' => $roomTypes->count(),
                'room_types_with_prices' => $formatted->where('stats.total_prices', '>', 0)->count(),
                'total_price_records' => $formatted->sum('stats.total_prices'),
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// ✅ Simple room name formatter
private function formatRoomName($room)
{
    return 'Room ' . $room->room_number . 
           ($room->floor ? ' (Floor ' . $room->floor . ')' : '');
}

    public function getPricesForRoomType($roomTypeId)
    {
        try {
            $roomType = RoomType::find($roomTypeId);
            
            if (!$roomType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room type not found'
                ], 404);
            }
            
            // Get prices for this room type
            $prices = ServicePrice::with(['category', 'room'])
                ->where('room_type_id', $roomTypeId)
                ->where('request_status', 'active')
                ->orderBy('service_category_id')
                ->orderBy('room_id')
                ->orderBy('duration_type')
                ->orderBy('duration')
                ->get();
            
            // Separate with category vs without category
            $withCategory = $prices->filter(function($price) {
                return !is_null($price->service_category_id);
            })->groupBy('service_category_id');
            
            $withoutCategory = $prices->filter(function($price) {
                return is_null($price->service_category_id);
            });
            
            // Format response
            $formattedWithCategory = [];
            foreach ($withCategory as $categoryId => $categoryPrices) {
                $category = ServiceCategory::find($categoryId);
                
                $formattedWithCategory[] = [
                    'category' => $category ? [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $this->createSlug($category->name)
                    ] : null,
                    'prices' => $categoryPrices->map(function($price) {
                        return $this->formatPriceData($price);
                    }),
                    'count' => $categoryPrices->count()
                ];
            }
            
            return response()->json([
                'success' => true,
                'room_type' => [
                    'id' => $roomType->id,
                    'name' => $roomType->name
                ],
                'pricing_data' => [
                    'with_service_category' => $formattedWithCategory,
                    'without_service_category' => $withoutCategory->map(function($price) {
                        return $this->formatPriceData($price);
                    }),
                ],
                'stats' => [
                    'total_prices' => $prices->count(),
                    'with_category' => $withCategory->count(),
                    'without_category' => $withoutCategory->count(),
                    'room_specific' => $prices->whereNotNull('room_id')->count(),
                    'general' => $prices->whereNull('room_id')->count(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

        private function calculatePercentageChange($oldPrice, $newPrice)
    {
        if ($oldPrice == 0) return null;
        
        $change = (($newPrice - $oldPrice) / $oldPrice) * 100;
        return round($change, 2);
    }
}