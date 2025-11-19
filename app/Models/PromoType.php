<?php
// app/Models/PromoType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(PromoCategory::class);
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }
}