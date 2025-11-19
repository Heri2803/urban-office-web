<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'alamat',
        'paket',
        'profile_photo',
        'google_id',
        'mitra_id', // ← TAMBAHKAN INI
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    /**
     * Cek apakah user adalah mitra yang approved/active
     */
    public function isMitra()
    {
        return $this->mitra_id !== null && 
               $this->mitra && 
               in_array($this->mitra->status, ['approved', 'active']);
    }

    /**
     * Cek apakah punya pendaftaran mitra pending
     */
    public function hasPendingMitra()
    {
        return $this->mitra_id !== null && 
               $this->mitra && 
               $this->mitra->status === 'pending';
    }

    /**
     * Cek apakah customer biasa (bukan mitra)
     */
    public function isCustomer()
    {
        return $this->mitra_id === null || 
               !$this->mitra || 
               !in_array($this->mitra->status, ['approved', 'active']);
    }
}