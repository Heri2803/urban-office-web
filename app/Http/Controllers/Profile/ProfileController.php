<?php
namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Transaction;


class ProfileController extends Controller
{
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // opsional: pastikan hanya user yang login bisa edit dirinya sendiri
        if ($user->id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('layouts.components.editprofile', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'alamat'    => 'nullable|string|max:255',
            'paket'     => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Upload foto kalau ada
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        return redirect()->route('dashboard.home')->with('success', 'Profil berhasil diperbarui!');
    }

    public function showDashboard()
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $user = Auth::user();

    $latestTransaction = Transaction::where('user_id', $user->id)
        ->orderByDesc('created_at') 
        ->orderByDesc('id')         
        ->first();

    if ($latestTransaction) {
        $status = $latestTransaction->room_type;
    } else {
        $status = 'Guest';
    }

    // PASTIKAN VIEW PATH SESUAI dengan view yang ingin ditampilkan
    // Jika ingin tampil di halaman dashboard home:
    return view('layouts.dashboard.home', [
        'user' => $user,
        'status' => $status,
    ]);
    
    // Atau jika ingin buat view khusus profile:
    // return view('layouts.profile.dashboard', [
    //     'user' => $user,
    //     'status' => $status,
    // ]);
}
}
