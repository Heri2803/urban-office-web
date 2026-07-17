<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoUsage extends Model
{
    use HasFactory;

    protected $table = 'promo_usages';

    protected $fillable = [
        'promo_id',
        'user_id',
        'transaction_id',
        'location',
        'discount_amount',
        'transaction_amount',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'discount_amount' => 'decimal:2',
        'transaction_amount' => 'decimal:2'
    ];

    /**
     * Relationship dengan promo
     */
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    /**
     * Relationship dengan user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship dengan transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Scopes
    public function scopeByPromo($query, $promoId)
    {
        return $query->when($promoId, function ($q) use ($promoId) {
            return $q->where('promo_id', $promoId);
        });
    }

    public function scopeByLocation($query, $location)
    {
        return $query->when($location, function ($q) use ($location) {
            return $q->where('location', $location);
        });
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->when($from, function ($q) use ($from) {
             return $q->whereDate('created_at', '>=', $from);
        })->when($to, function ($q) use ($to) {
             return $q->whereDate('created_at', '<=', $to);
        });
    }

    public function scopeByUser($query, $userId)
    {
        return $query->when($userId, function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        });
    }

    // Accessors
    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format((float) $this->discount_amount, 0, ',', '.');
    }

    public function getFormattedTransactionAmountAttribute()
    {
        return 'Rp ' . number_format((float) $this->transaction_amount, 0, ',', '.');
    }
}
