<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $fillable = [
        'nama_lengkap_ktp',
        'alamat_email',
        'alamat_properti',
        'nik',
        'foto_properti_path',
        'status',
    ];

    // Opsional: Pastikan kolom tanggal adalah Carbon instance
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}