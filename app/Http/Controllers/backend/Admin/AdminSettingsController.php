<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminSettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Pastikan user adalah admin
        // if ($user->role !== 'admin') {
        //     abort(403, 'Unauthorized access');
        // }
        
        return view('layouts.admin.settings', [
            'user' => $user,
            'title' => 'Settings',
            'accountInfo' => [
                'status' => 'Active',
                'created_at' => $user->created_at->format('F j, Y'),
                'last_login' => 'October 14, 2025 - 09:30 AM',
                'branch' => 'Jakarta Central - JKT001',
                'schedule' => 'Mon-Fri, 08:00 - 17:00',
                'role' => 'Admin Receptionist',
                'employee_id' => 'EMP-2025-001'
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->full_name,
            'telephone' => $request->phone_number, // Sesuai field di database
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => [
                'name' => $user->name,
                'telephone' => $user->telephone,
                'email' => $user->email
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old avatar if exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('profile_photos', 'public');
        
        $user->update([
            'profile_photo' => $path // Sesuai field di database
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
            'avatar_url' => Storage::url($path)
        ]);
    }

    // ✅ Method untuk remove avatar
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo removed successfully!',
            'avatar_url' => $this->generateDefaultAvatar($user->name)
        ]);
    }

    // ✅ Generate default avatar URL
    private function generateDefaultAvatar($name)
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=200&background=3B82F6&color=fff';
    }
}