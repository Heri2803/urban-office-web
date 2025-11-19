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

    // SEMENTARA NONAKTIFKAN SEMUA SCOPE - COMMENT DULU
    /*
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('is_approved', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('start_date', '>', now())
                    ->where('is_approved', true);
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended')
                    ->orWhere(function($q) {
                        $q->where('end_date', '<', now())
                          ->whereIn('status', ['active', 'upcoming']);
                    });
    }

    public function scopeForLocation($query, $locationId)
    {
        // Jika location adalah ID cabang (number)
        if (is_numeric($locationId)) {
            return $query->whereJsonContains('locations', (int)$locationId);
        }
        
        return $query;
    }

    public function scopeForService($query, $service)
    {
        return $query->whereJsonContains('service_types', $service)
                    ->orWhere('service_types', 'like', '%all-services%');
    }

    public function scopeBanners($query)
    {
        return $query->whereHas('type', function($q) {
            $q->where('slug', 'banner');
        });
    }

    public function scopeDiscounts($query)
    {
        return $query->whereHas('type', function($q) {
            $q->where('slug', 'discount');
        });
    }
    */

    // Accessors & Mutators
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now() &&
               $this->is_approved;
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