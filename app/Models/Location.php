<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address','city_id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'location_id');
    }
    public function rooms()
    {
        return $this->hasMany(Room::class, 'location_id');
    }
    public function monthlyTaxReports(): HasMany
    {
        return $this->hasMany(MonthlyTaxReport::class);
    }

    // ========== TAMBAHKAN RELASI KE USERS ==========
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    public function customers()
    {
        return $this->hasMany(User::class)->where('role', 'customer');
    }
}
