<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\ServiceHighlight;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ServiceHighlightController extends Controller
{
    /**
     * Get initial data for service highlights page
     */
    public function getInitialData(): JsonResponse
    {
        \Log::info('=== getInitialData called ===');
        
        try {
            \Log::info('Fetching RoomTypes...');
            $roomTypes = RoomType::with(['rooms' => function($query) {
                $query->active()->orderBy('room_number'); // ✅ GANTI dari 'name' ke 'room_number'
            }])->active()->get();
            \Log::info('RoomTypes fetched: ' . $roomTypes->count());

            \Log::info('Fetching Highlights...');
            $highlights = ServiceHighlight::with(['roomTypes', 'rooms'])
                ->ordered()
                ->get();
            \Log::info('Highlights fetched: ' . $highlights->count());

            \Log::info('Fetching Rooms...');
            $rooms = Room::active()->with('roomType')->get();
            \Log::info('Rooms fetched: ' . $rooms->count());

            \Log::info('=== Data fetched successfully ===');

            return response()->json([
                'success' => true,
                'data' => [
                    'roomTypes' => $roomTypes,
                    'highlights' => $highlights,
                    'rooms' => $rooms
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('=== ERROR in getInitialData ===');
            \Log::error('Message: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created highlight
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'show_in_all_services' => 'boolean',
                'selected_room_types' => 'array',
                'selected_room_types.*' => 'exists:room_types,id',
                'selected_rooms' => 'array', 
                'selected_rooms.*' => 'exists:rooms,id'
            ]);

            // Create highlight
            $highlight = ServiceHighlight::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'show_in_all_services' => $validated['show_in_all_services'] ?? false,
                'sort_order' => ServiceHighlight::max('sort_order') + 1
            ]);

            // Attach room types if not showing in all services
            if (!$highlight->show_in_all_services && isset($validated['selected_room_types'])) {
                $highlight->roomTypes()->sync($validated['selected_room_types']);
            }

            // Attach specific rooms if provided
            if (isset($validated['selected_rooms'])) {
                $highlight->rooms()->sync($validated['selected_rooms']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Highlight created successfully',
                'highlight' => $highlight->load(['roomTypes', 'rooms'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create highlight'
            ], 500);
        }
    }

    /**
     * Update the specified highlight
     */
    public function update(Request $request, ServiceHighlight $serviceHighlight): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'show_in_all_services' => 'boolean',
                'selected_room_types' => 'array',
                'selected_room_types.*' => 'exists:room_types,id',
                'selected_rooms' => 'array',
                'selected_rooms.*' => 'exists:rooms,id'
            ]);

            // Update highlight
            $serviceHighlight->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'show_in_all_services' => $validated['show_in_all_services'] ?? false
            ]);

            // Sync room types
            if ($serviceHighlight->show_in_all_services) {
                $serviceHighlight->roomTypes()->detach();
            } else {
                $serviceHighlight->roomTypes()->sync($validated['selected_room_types'] ?? []);
            }

            // Sync rooms
            $serviceHighlight->rooms()->sync($validated['selected_rooms'] ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Highlight updated successfully',
                'highlight' => $serviceHighlight->load(['roomTypes', 'rooms'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update highlight'
            ], 500);
        }
    }

    /**
     * Remove the specified highlight
     */
    public function destroy(ServiceHighlight $serviceHighlight): JsonResponse
    {
        try {
            $serviceHighlight->roomTypes()->detach();
            $serviceHighlight->rooms()->detach();
            $serviceHighlight->delete();

            return response()->json([
                'success' => true,
                'message' => 'Highlight deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete highlight'
            ], 500);
        }
    }

    /**
     * Toggle highlight active status
     */
    public function toggleStatus(ServiceHighlight $serviceHighlight): JsonResponse
    {
        try {
            $serviceHighlight->update([
                'is_active' => !$serviceHighlight->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'is_active' => $serviceHighlight->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Reorder highlights
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|exists:service_highlights,id',
                'order.*.sort_order' => 'required|integer'
            ]);

            foreach ($request->order as $item) {
                ServiceHighlight::where('id', $item['id'])
                    ->update(['sort_order' => $item['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order'
            ], 500);
        }
    }
}