<?php
// app/Models/MonthlyTaxReport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MonthlyTaxReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id', 'period', 'total_revenue', 'tax_amount', 'status', 
        'invoice_number', 'notes', 'reported_at', 'reported_by'
    ];

    protected $casts = [
        'period' => 'date',
        'reported_at' => 'datetime',
        'total_revenue' => 'decimal:2',
        'tax_amount' => 'decimal:2'
    ];

    // Cache duration (1 jam)
    const CACHE_DURATION_HOURS = 1;

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Accessor: Total Revenue - Hybrid (Cached + Real-time)
     */
    public function getTotalRevenueAttribute()
    {
        // Jika data masih fresh (< 1 jam) dan ada nilai, gunakan cached value
        $isFresh = $this->updated_at && 
                  $this->updated_at->gt(now()->subHours(self::CACHE_DURATION_HOURS));
        
        if ($isFresh && 
            isset($this->attributes['total_revenue']) && 
            $this->attributes['total_revenue'] > 0) {
            Log::info("Using cached revenue for {$this->location->name} {$this->formatted_period}: {$this->attributes['total_revenue']}");
            return (float) $this->attributes['total_revenue'];
        }

        // Jika data outdated, hitung real-time dari transactions
        Log::info("Calculating real-time revenue for {$this->location->name} {$this->formatted_period}");
        
        $revenue = Transaction::where('location_id', $this->location_id)
            ->whereIn('status', ['settlement', 'capture', 'paid'])
            ->whereYear('booking_date', $this->period->year)
            ->whereMonth('booking_date', $this->period->month)
            ->sum('gross_amount');

        $revenue = (float) $revenue;

        // Auto-update database untuk caching (hanya jika berbeda signifikan)
        $currentValue = (float) ($this->attributes['total_revenue'] ?? 0);
        $isSignificantlyDifferent = abs($revenue - $currentValue) > 1000; // Bedanya > Rp 1.000

        if ($isSignificantlyDifferent) {
            Log::info("Updating cache: {$this->location->name} {$this->formatted_period} - From: {$currentValue} To: {$revenue}");
            $this->updateQuietly(['total_revenue' => $revenue]);
        }

        return $revenue;
    }

    /**
     * Accessor: Tax Amount - Hybrid
     */
    public function getTaxAmountAttribute()
    {
        // Gunakan cached value jika masih fresh
        $isFresh = $this->updated_at && 
                  $this->updated_at->gt(now()->subHours(self::CACHE_DURATION_HOURS));
        
        if ($isFresh && 
            isset($this->attributes['tax_amount']) && 
            $this->attributes['tax_amount'] > 0) {
            return (float) $this->attributes['tax_amount'];
        }

        // Hitung real-time
        $tax = $this->total_revenue * 0.1;
        $tax = (float) $tax;

        // Auto-update cache
        $currentValue = (float) ($this->attributes['tax_amount'] ?? 0);
        $isSignificantlyDifferent = abs($tax - $currentValue) > 100; // Bedanya > Rp 100

        if ($isSignificantlyDifferent) {
            $this->updateQuietly(['tax_amount' => $tax]);
        }

        return $tax;
    }

    /**
     * Accessor: Cek apakah punya data
     */
    public function getHasDataAttribute()
    {
        return $this->total_revenue > 0;
    }

    /**
     * Force refresh data (untuk manual update)
     */
    public function refreshData()
    {
        $revenue = Transaction::where('location_id', $this->location_id)
            ->whereIn('status', ['settlement', 'capture', 'paid'])
            ->whereYear('booking_date', $this->period->year)
            ->whereMonth('booking_date', $this->period->month)
            ->sum('gross_amount');

        $tax = $revenue * 0.1;

        $this->update([
            'total_revenue' => $revenue,
            'tax_amount' => $tax,
            'updated_at' => now() // Reset cache timer
        ]);

        return $this;
    }

    /**
     * Get formatted period
     */
    public function getFormattedPeriodAttribute()
    {
        return $this->period->translatedFormat('F Y');
    }

    /**
     * Get year from period
     */
    public function getYearAttribute()
    {
        return $this->period->year;
    }
}