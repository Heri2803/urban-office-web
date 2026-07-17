<?php
// app/Models/Promo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'description', 'promo_type_id', 'promo_category_id',
        'status', 'start_date', 'end_date', 'priority', 'locations', 'service_types',
        'image_url', 'thumbnail_url', 'images', 'discount_amount', 'discount_type',
        'min_transaction', 'usage_limit', 'usage_per_user', 'view_count', 'click_count',
        'usage_count', 'is_approved', 'approved_at', 'approved_by', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'locations' => 'array',
        'service_types' => 'array',
        'images' => 'array',
        'discount_amount' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'is_approved' => 'boolean',
        'usage_limit' => 'integer',
        'usage_per_user' => 'integer',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'usage_count' => 'integer',
        'priority' => 'integer'
    ];

    // Relationships
    public function type(): BelongsTo
    {
        return $this->belongsTo(PromoType::class, 'promo_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PromoCategory::class, 'promo_category_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(PromoMetric::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoUsage::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'promo_user')
                    ->withPivot('is_used', 'used_at', 'is_claimed', 'claimed_at')
                    ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors & Mutators
    public function getIsActiveAttribute(): bool
    {
        $now = now();

        // Promo berlaku sepanjang hari start_date s/d end_date (bukan jam presisi),
        // jadi bandingkan pakai awal/akhir hari — konsisten dengan BannerController::apiIndex().
        return $this->status === 'active' &&
               $now->gte($this->start_date->copy()->startOfDay()) &&
               $now->lte($this->end_date->copy()->endOfDay()) &&
               $this->is_approved;
    }

    public function getImageUrlAttribute($value): ?string
    {
        if (!$value) return null;
        
        if (str_starts_with($value, 'http') || str_starts_with($value, '/storage/')) {
            return $value;
        }

        return asset('storage/' . $value);
    }

    public function getDaysRemainingAttribute(): int
    {
        return now()->diffInDays($this->end_date, false);
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->usages()->sum('transaction_amount');
    }

    public function getTotalDiscountAttribute(): float
    {
        return $this->usages()->sum('discount_amount');
    }

    // Business Logic
    public function canBeUsed(): bool
    {
        return $this->is_active &&
               $this->usage_count < ($this->usage_limit ?? PHP_INT_MAX);
    }

    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function incrementClick(): void
    {
        $this->increment('click_count');
    }

    public function calculateImpact(string $location): array
    {
        $metrics = $this->metrics()->where('location', $location)->first();
        
        if (!$metrics) {
            return [
                'transactionIncrease' => null,
                'revenueIncrease' => null,
                'before' => null,
                'during' => null,
                'after' => null
            ];
        }

        // Calculate impact logic here
        return [
            'transactionIncrease' => 25.5, // Example calculation
            'revenueIncrease' => 18.3,
            'before' => ['transactions' => 45, 'revenue' => 8500000],
            'during' => ['transactions' => 99, 'revenue' => 16600000, 'promoUsage' => 85],
            'after' => ['transactions' => 56, 'revenue' => 10500000]
        ];
    }
}