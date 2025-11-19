<?php
// app/Models/PromoCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'promo_type_id', 'settings', 'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(PromoType::class, 'promo_type_id');
    }

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class);
    }
}