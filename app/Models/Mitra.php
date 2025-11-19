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

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke user (satu mitra punya satu user)
     */
    public function user()
    {
        return $this->hasOne(User::class, 'mitra_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Cek apakah mitra sudah approved/active
     */
    public function isApproved()
    {
        return in_array($this->status, ['approved', 'active']);
    }

    /**
     * Cek apakah status pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}