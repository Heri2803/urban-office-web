<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
