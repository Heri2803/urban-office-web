<?php

namespace App\Http\Controllers\Mitra;

use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller; 

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
        Mitra::create([
            'nama_lengkap_ktp' => $validatedData['namaLengkap'],
            'alamat_email'     => $validatedData['alamatEmail'],
            'alamat_properti'  => $validatedData['alamatProperti'],
            'nik'              => $validatedData['nik'],
            'foto_properti_path' => $path, // Simpan path file
            'status'           => 'pending', // Default status
        ]);

        // 4. Redirect dan Beri Pesan Sukses
        return redirect()->route('dashboard.home')->with('success', 'Aplikasi kemitraan Anda berhasil dikirim! Kami akan segera menghubungi Anda.');
    }
}