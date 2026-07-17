<?php
// app/Models/LunchOption.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocationScope;

class LunchOption extends Model
{
    use HasFactory, HasLocationScope;

    protected $fillable = [
        'name',
        'description', 
        'price',
        'is_available',
        'location_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean'
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transactionLunches()
    {
        return $this->hasMany(TransactionLunch::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByLocation($query, $locationId = null)
    {
        if ($locationId) {
            return $query->where('location_id', $locationId)
                        ->orWhereNull('location_id');
        }
        return $query;
    }
}