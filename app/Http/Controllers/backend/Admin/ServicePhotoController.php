<?php
// app/Http/Controllers/Backend/Admin/ServicePhotoController.php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\ServicePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServicePhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $photos = ServicePhoto::with('roomType')
            ->orderBy('room_type_id')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($photos);
    }

    /**
     * Get all room types
     */
    public function getRoomTypes()
    {
        try {
            // ✅ SIMPLE: Ambil semua room type, urutkan by name
            $roomTypes = RoomType::orderBy('name')->get();

            return response()->json($roomTypes);

        } catch (\Exception $e) {
            \Log::error('Error in getRoomTypes: ' . $e->getMessage());
            
            return response()->json([
                'error' => $e->getMessage(),
                'roomTypes' => []
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // ✅ 2MB
            'caption' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $roomType = RoomType::findOrFail($request->room_type_id);
            $file = $request->file('photo');
            
            // ✅ Validasi size tambahan (safety net)
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
            
            // Check if this is the first photo for this room type
            $isFirstPhoto = ServicePhoto::where('room_type_id', $request->room_type_id)->count() === 0;

            // Create photo record
            $photo = ServicePhoto::create([
                'room_type_id' => $request->room_type_id,
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
                'photo' => $photo->load('roomType'),
                'message' => 'Photo uploaded successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

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
            // Validasi dasar
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

                // Hapus file lama jika ada
                if ($servicePhoto->file_path && Storage::exists($servicePhoto->file_path)) {
                    Storage::delete($servicePhoto->file_path);
                }

                // Upload file baru
                $filename = $validatedData['filename'];
                $filePath = $file->store('service-photos', 'public');

                // Update dengan file baru
                $servicePhoto->update([
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
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

            // Handle primary photo setting
            $isPrimary = filter_var($request->input('is_primary', false), FILTER_VALIDATE_BOOLEAN);
            
            if ($isPrimary && !$servicePhoto->is_primary) {
                // Reset semua primary photo untuk room type ini
                ServicePhoto::where('room_type_id', $servicePhoto->room_type_id)
                    ->where('id', '!=', $servicePhoto->id)
                    ->update(['is_primary' => false]);
                
                $servicePhoto->update(['is_primary' => true]);
            } elseif (!$isPrimary && $servicePhoto->is_primary) {
                $servicePhoto->update(['is_primary' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo' => $servicePhoto->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

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
            $servicePhoto->setAsPrimary();

            return response()->json([
                'success' => true,
                'message' => 'Photo set as primary successfully'
            ]);

        } catch (\Exception $e) {
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
            $wasPrimary = $servicePhoto->is_primary;
            $filePath = $servicePhoto->file_path;

            // Delete the photo record
            $servicePhoto->delete();

            // Delete the physical file
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // If deleted photo was primary, set a new primary
            if ($wasPrimary) {
                $newPrimary = ServicePhoto::where('room_type_id', $roomTypeId)
                    ->orderBy('created_at', 'asc')
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
            'room_type_id' => 'required|exists:room_types,id',
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $uploadedPhotos = [];
            $roomTypeId = $request->room_type_id;
            
            // Check if this room type has any photos
            $hasExistingPhotos = ServicePhoto::where('room_type_id', $roomTypeId)->exists();

            foreach ($request->file('photos') as $file) {
                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($originalName) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
                
                // Store file
                $filePath = $file->storeAs('service-photos', $filename, 'public');
                
                // Create photo record
                $photo = ServicePhoto::create([
                    'room_type_id' => $roomTypeId,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_url' => Storage::disk('public')->url($filePath),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'caption' => '',
                    'is_primary' => !$hasExistingPhotos, // Set as primary only if no existing photos
                    'uploaded_by' => auth()->id(),
                ]);

                $uploadedPhotos[] = $photo;
                $hasExistingPhotos = true; // After first photo, subsequent ones are not primary
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