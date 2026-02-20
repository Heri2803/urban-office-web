<?php
// app/Http\Controllers\Backend\Admin\ServicePhotoController.php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Location;
use App\Models\Room;
use App\Models\ServicePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ServicePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServicePhoto::with(['roomType', 'location', 'room'])
            ->orderBy('room_type_id')
            ->orderBy('room_id')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter by location
        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }
        
        // Filter by room type
        if ($request->has('room_type_id') && $request->room_type_id) {
            $query->where('room_type_id', $request->room_type_id);
        }
        
        // ✅ NEW: Filter by room
        if ($request->has('room_id') && $request->room_id) {
            $query->where('room_id', $request->room_id);
        }
        
        $photos = $query->get();
        
        return response()->json($photos);
    }

    /**
     * Get all locations
     */
    public function getLocations()
    {
        try {
            $locations = Location::withCount('servicePhotos')
                ->orderBy('name')
                ->get();
                
            return response()->json($locations);
        } catch (\Exception $e) {
            Log::error('Error loading locations: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all room types (with optional location filter)
     */
    public function getRoomTypes(Request $request)
    {
        try {
            $query = RoomType::query();
            
            if ($request->has('location_id') && $request->location_id) {
                $query->whereHas('rooms', function($q) use ($request) {
                    $q->where('location_id', $request->location_id);
                });
            }
            
            $roomTypes = $query->orderBy('name')->get();
            
            return response()->json($roomTypes);

        } catch (\Exception $e) {
            Log::error('Error in getRoomTypes: ' . $e->getMessage());
            
            return response()->json([
                'error' => $e->getMessage(),
                'roomTypes' => []
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get rooms (with optional location and room type filters)
     */
        public function getRooms(Request $request)
    {
        try {
            $query = Room::with(['roomType', 'location']);
            
            if ($request->has('location_id') && $request->location_id) {
                $query->where('location_id', $request->location_id);
            }
            
            if ($request->has('room_type_id') && $request->room_type_id) {
                $query->where('room_type_id', $request->room_type_id);
            }
            
            // Get rooms dengan photo count menggunakan withCount
            $rooms = $query->withCount('servicePhotos')
                ->orderBy('room_number')
                ->get()
                ->map(function($room) {
                    // ✅ PERBAIKI: Handle null roomType dan location
                    $roomTypeName = $room->roomType ? $room->roomType->name : 'N/A';
                    $locationName = $room->location ? $room->location->name : 'N/A';
                    
                    return [
                        'id' => $room->id,
                        'room_number' => $room->room_number, // ✅ GANTI: name -> room_number
                        'room_type_id' => $room->room_type_id,
                        'room_type_name' => $roomTypeName, // ✅ PERBAIKI: handle null
                        'location_id' => $room->location_id,
                        'location_name' => $locationName, // ✅ PERBAIKI: handle null
                        'floor' => $room->floor,
                        'capacity' => $room->capacity,
                        'status' => $room->status,
                        'service_photos_count' => $room->service_photos_count ?? 0,
                        'display_name' => "{$room->room_number} - {$roomTypeName}"
                    ];
                });
                
            return response()->json($rooms);
        } catch (\Exception $e) {
            \Log::error('Error loading rooms: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id', // ✅ NEW: room_id optional
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'caption' => 'nullable|string|max:500'
        ]);
    
        try {
            DB::beginTransaction();
    
            // ✅ NEW: Validasi room consistency
            if ($request->room_id) {
                $room = Room::find($request->room_id);
                if (!$room) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Room not found'
                    ], 404);
                }
                
                // Pastikan room milik location dan room_type yang dipilih
                if ($room->location_id != $request->location_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Room does not belong to selected location'
                    ], 422);
                }
                
                if ($room->room_type_id != $request->room_type_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Room does not match selected room type'
                    ], 422);
                }
            }
    
            $file = $request->file('photo');
            
            // Validasi size
            if ($file->getSize() > 2 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File size exceeds 2MB limit'
                ], 422);
            }
            
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($originalName) . '_' . time() . '.' . $extension;
            
            // Store file
            $filePath = $file->storeAs('service-photos', $filename, 'public');
            
            // ✅ UPDATE: is_primary logic dengan room_id
            $query = ServicePhoto::where('location_id', $request->location_id)
                ->where('room_type_id', $request->room_type_id);
            
            if ($request->room_id) {
                // Jika ada room_id, cek apakah ini foto pertama untuk room tersebut
                $query->where('room_id', $request->room_id);
            } else {
                // Jika tidak ada room_id, cek untuk semua rooms dengan type tersebut
                $query->whereNull('room_id');
            }
            
            $isFirstPhoto = $query->count() === 0;
    
            // Create photo record
            $photo = ServicePhoto::create([
                'location_id' => $request->location_id,
                'room_type_id' => $request->room_type_id,
                'room_id' => $request->room_id, // ✅ NEW
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_url' => Storage::disk('public')->url($filePath),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'caption' => $request->caption,
                'is_primary' => $isFirstPhoto,
                'uploaded_by' => auth()->id(),
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'photo' => $photo->load(['roomType', 'location', 'room']),
                'message' => 'Photo uploaded successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            Log::error('Upload photo error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServicePhoto $servicePhoto)
    {
        try {
            $validatedData = $request->validate([
                'filename' => 'required|string|max:255',
                'caption' => 'nullable|string',
                'is_primary' => 'sometimes|boolean',
                'new_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
    
            DB::beginTransaction();
    
            // Handle file upload jika ada file baru
            if ($request->hasFile('new_photo')) {
                $file = $request->file('new_photo');
                
                // Hapus file lama
                if ($servicePhoto->file_path && Storage::disk('public')->exists($servicePhoto->file_path)) {
                    Storage::disk('public')->delete($servicePhoto->file_path);
                }
                
                // Upload file baru
                $newFilename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $newFilePath = $file->storeAs('service-photos', $newFilename, 'public');
                
                // Update dengan file baru
                $servicePhoto->update([
                    'filename' => $validatedData['filename'],
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $newFilePath,
                    'file_url' => Storage::disk('public')->url($newFilePath),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'caption' => $validatedData['caption'] ?? $servicePhoto->caption,
                ]);
            } else {
                // Update tanpa mengganti file
                $servicePhoto->update([
                    'filename' => $validatedData['filename'],
                    'caption' => $validatedData['caption'] ?? $servicePhoto->caption,
                ]);
            }
    
            // ✅ UPDATE: Handle primary photo setting DENGAN room_id
            $isPrimary = filter_var($request->input('is_primary', false), FILTER_VALIDATE_BOOLEAN);
            
            if ($isPrimary && !$servicePhoto->is_primary) {
                // Reset primary berdasarkan scope yang sama
                $query = ServicePhoto::where('location_id', $servicePhoto->location_id)
                    ->where('room_type_id', $servicePhoto->room_type_id);
                
                if ($servicePhoto->room_id) {
                    $query->where('room_id', $servicePhoto->room_id);
                } else {
                    $query->whereNull('room_id');
                }
                
                $query->where('id', '!=', $servicePhoto->id)
                    ->update(['is_primary' => false]);
                
                $servicePhoto->update(['is_primary' => true]);
            } elseif (!$isPrimary && $servicePhoto->is_primary) {
                $servicePhoto->update(['is_primary' => false]);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo' => $servicePhoto->fresh()->load(['roomType', 'location', 'room'])
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Update photo error: ' . $e->getMessage(), [
                'photo_id' => $servicePhoto->id
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set photo as primary
     */
    public function setPrimary(ServicePhoto $servicePhoto)
    {
        try {
            DB::beginTransaction();
            
            // ✅ UPDATE: Reset primary berdasarkan scope yang sama
            $query = ServicePhoto::where('location_id', $servicePhoto->location_id)
                ->where('room_type_id', $servicePhoto->room_type_id);
            
            if ($servicePhoto->room_id) {
                $query->where('room_id', $servicePhoto->room_id);
            } else {
                $query->whereNull('room_id');
            }
            
            $query->where('id', '!=', $servicePhoto->id)
                ->update(['is_primary' => false]);
            
            $servicePhoto->update(['is_primary' => true]);
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Photo set as primary successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Set primary error: ' . $e->getMessage(), [
                'photo_id' => $servicePhoto->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicePhoto $servicePhoto)
    {
        try {
            DB::beginTransaction();
    
            $roomTypeId = $servicePhoto->room_type_id;
            $locationId = $servicePhoto->location_id;
            $roomId = $servicePhoto->room_id;
            $wasPrimary = $servicePhoto->is_primary;
            
            // Hapus file fisik
            if ($servicePhoto->file_path && Storage::disk('public')->exists($servicePhoto->file_path)) {
                Storage::disk('public')->delete($servicePhoto->file_path);
            }
    
            // Delete the photo record
            $servicePhoto->delete();
    
            // ✅ UPDATE: Jika deleted photo was primary, set new primary berdasarkan scope yang sama
            if ($wasPrimary) {
                $query = ServicePhoto::where('location_id', $locationId)
                    ->where('room_type_id', $roomTypeId);
                
                if ($roomId) {
                    $query->where('room_id', $roomId);
                } else {
                    $query->whereNull('room_id');
                }
                
                $newPrimary = $query->orderBy('created_at', 'asc')
                    ->first();
    
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Photo deleted successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Delete photo error: ' . $e->getMessage(), [
                'photo_id' => $servicePhoto->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk upload multiple photos
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'room_type_id' => 'required|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id', // ✅ NEW
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);
    
        try {
            DB::beginTransaction();
    
            $uploadedPhotos = [];
            $locationId = $request->location_id;
            $roomTypeId = $request->room_type_id;
            $roomId = $request->room_id;
            
            // ✅ UPDATE: Check existing photos dengan room_id
            $query = ServicePhoto::where('location_id', $locationId)
                ->where('room_type_id', $roomTypeId);
            
            if ($roomId) {
                $query->where('room_id', $roomId);
            } else {
                $query->whereNull('room_id');
            }
            
            $hasExistingPhotos = $query->exists();
    
            foreach ($request->file('photos') as $file) {
                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
                
                // Store file
                $filePath = $file->storeAs('service-photos', $filename, 'public');
                
                // Create photo record
                $photo = ServicePhoto::create([
                    'location_id' => $locationId,
                    'room_type_id' => $roomTypeId,
                    'room_id' => $roomId, // ✅ NEW
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_url' => Storage::disk('public')->url($filePath),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'caption' => '',
                    'is_primary' => !$hasExistingPhotos,
                    'uploaded_by' => auth()->id(),
                ]);
    
                $uploadedPhotos[] = $photo;
                $hasExistingPhotos = true;
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'photos' => $uploadedPhotos,
                'message' => count($uploadedPhotos) . ' photos uploaded successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            // Delete any uploaded files
            foreach ($uploadedPhotos as $photo) {
                if (Storage::disk('public')->exists($photo->file_path)) {
                    Storage::disk('public')->delete($photo->file_path);
                }
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photos: ' . $e->getMessage()
            ], 500);
        }
    }
}