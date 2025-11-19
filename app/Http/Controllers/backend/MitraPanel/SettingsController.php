<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        return view('layouts.mitrapanel.settings'); // Sesuaikan dengan path view Anda
    }

    /**
     * Get profile data for logged in user
     */
    public function getProfileData(Request $request)
    {
        try {
            $user = Auth::user();
            
            \Log::info('🔍 DEBUG - Using user data only');

            // ✅ GUNAKAN DATA USER SAJA (KARENA MITRA DATA MASIH NULL)
            $profileData = [
                'name' => $user->name, // "Heriyanto"
                'email' => $user->email, // "muhammadheriyanto090@gmail.com"
                'phone' => $user->telephone, // "0871414091"
                'address' => $user->alamat, // "tulangan"
                'photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                'company_name' => $user->name, // Default ke nama user
                'business_type' => '',
                'npwp' => '',
            ];

            \Log::info('✅ FINAL - Profile data (user only):', $profileData);

            return response()->json([
                'success' => true,
                'profile' => $profileData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getProfileData: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile data
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            \Log::info('🔄 Starting profile update for user:', ['user_id' => $user->id]);

            // ✅ VALIDASI
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:15',
                'address' => 'required|string',
                'company_name' => 'required|string|max:255',
                'business_type' => 'nullable|string',
                'npwp' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            \Log::info('✅ Validation passed');

            // ✅ UPDATE USER DATA
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'telephone' => $validated['phone'],
                'alamat' => $validated['address']
            ];

            $user->update($userData);
            \Log::info('✅ User data updated');

            // ✅ HANDLE MITRA DATA - DENGAN CHECK YANG LEBIH AMAN
            if ($user->mitra) {
                $user->mitra->update([
                    'company_name' => $validated['company_name'],
                    'business_type' => $validated['business_type'],
                    'npwp' => $validated['npwp']
                ]);
                \Log::info('✅ Mitra data updated');
            } else {
                \Log::warning('❌ Mitra relation not found for user:', ['user_id' => $user->id]);
                // Atau buat data mitra baru jika diperlukan
                // Mitra::create([...]);
            }

            // ✅ HANDLE PHOTO UPLOAD
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                \Log::info('🔄 Processing photo upload');
                
                // Hapus foto lama jika ada
                if ($user->profile_photo) {
                    $oldPhotoPath = str_replace('/storage/', '', $user->profile_photo);
                    if (\Storage::disk('public')->exists($oldPhotoPath)) {
                        \Storage::disk('public')->delete($oldPhotoPath);
                    }
                }
                
                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->update(['profile_photo' => $path]);
                \Log::info('✅ Photo uploaded:', ['path' => $path]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'photo_url' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error updating profile: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error updating profile: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Check current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini salah'
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update notifications
     */
    public function updateNotifications(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'settings' => 'required|array'
            ]);

            // Simpan preference notifications ke database
            // Sesuaikan dengan struktur database Anda
            $user->update([
                'notification_settings' => json_encode($validated['settings'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferensi notifikasi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan preferensi notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy update method (jika sudah ada)
     */
    public function update(Request $request)
    {
        // Method lama, bisa diisi atau dihapus tergantung kebutuhan
        return $this->updateProfile($request);
    }
}