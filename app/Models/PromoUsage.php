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
}