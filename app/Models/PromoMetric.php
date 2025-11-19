<?php
// app/Models/PromoMetric.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_id', 'location', 'metric_date', 'views', 'clicks', 
        'unique_visitors', 'transactions', 'revenue', 'promo_usage',
        'discount_amount_used', 'new_customers', 'returning_customers',
        'conversion_rate', 'revenue_per_click', 'avg_order_value'
    ];

    protected $casts = [
        'metric_date' => 'date',
        'revenue' => 'decimal:2',
        'discount_amount_used' => 'decimal:2',
        'revenue_per_click' => 'decimal:2',
        'avg_order_value' => 'decimal:2',
        'conversion_rate' => 'decimal:2'
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    // Calculate derived metrics
    public function calculateConversionRate(): float
    {
        return $this->views > 0 ? ($this->clicks / $this->views) * 100 : 0;
    }

    public function calculateRevenuePerClick(): float
    {
        return $this->clicks > 0 ? $this->revenue / $this->clicks : 0;
    }
}