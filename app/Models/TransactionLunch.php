<?php
// app/Models/TransactionLunch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLunch extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'lunch_option_id', 
        'quantity',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function lunchOption()
    {
        return $this->belongsTo(LunchOption::class);
    }

    // Calculate subtotal automatically
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->subtotal = $model->quantity * $model->unit_price;
        });
    }
}