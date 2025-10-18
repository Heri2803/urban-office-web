<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Mitra;
use App\Mail\MitraNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Log;


class MitraController extends Controller
{
    // Fungsi untuk menampilkan form (opsional, jika Anda memuat view dari sini)
    public function create()
    {
        return view('nama_view_form_mitra'); // Ganti dengan nama view form Anda
    }

    // Fungsi untuk menangani data submit dari form
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'namaLengkap'    => 'required|string|max:255',
            'alamatEmail'    => 'required|email|unique:mitra,alamat_email',
            'alamatProperti' => 'required|string',
            'nik'            => 'required|string|max:16|min:16|unique:mitra,nik',
            'fotoProperti'   => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ]);

        // 2. Upload File Foto Properti
        if ($request->hasFile('fotoProperti')) {
            // Simpan file di direktori 'mitra-properties' di storage/app/public
            $path = $request->file('fotoProperti')->store('mitra-properties', 'public');
        } else {
            // Ini seharusnya tidak terjadi karena validasi 'required'
            return redirect()->back()->with('error', 'Foto properti wajib diunggah.');
        }

        // 3. Simpan Data ke Database
        $mitra = Mitra::create([
            'nama_lengkap_ktp' => $validatedData['namaLengkap'],
            'alamat_email'     => $validatedData['alamatEmail'],
            'alamat_properti'  => $validatedData['alamatProperti'],
            'nik'              => $validatedData['nik'],
            'foto_properti_path' => $path,
            'status'           => 'pending',
        ]);

        // 4. Kirim Email ke User dan Admin
        try {
            // Email ke user yang submit form
            Mail::to($mitra->alamat_email)
                ->send(new MitraNotificationMail($mitra, true));

            // Email ke admin
            Mail::to('muhammadheriyanto28@gmail.com')
                ->send(new MitraNotificationMail($mitra, false));

            Log::info('Email kemitraan berhasil dikirim', [
                'user_email' => $mitra->alamat_email,
                'admin_email' => 'muhammadheriyanto28@gmail.com'
            ]);

        } catch (Exception $e) {
            // Log error tapi tetap redirect sukses
            // Karena data sudah tersimpan, hanya email yang gagal
            Log::error('Gagal mengirim email kemitraan: ' . $e->getMessage(), [
                'mitra_id' => $mitra->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dashboard.home')
                ->with('warning', 'Aplikasi kemitraan Anda berhasil dikirim, namun email notifikasi gagal terkirim. Tim kami akan segera menghubungi Anda.');
        }

        // 5. Redirect dan Beri Pesan Sukses
        return redirect()->route('dashboard.home')
            ->with('success', 'Aplikasi kemitraan Anda berhasil dikirim! Kami akan segera menghubungi Anda melalui email.');
    }
}