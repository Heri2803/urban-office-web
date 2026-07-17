<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBonus extends Model
{
    protected $fillable = [
        'user_id',
        'bonus_rule_id',
        'transaction_id',
        'bonus_hours_total',
        'bonus_hours_used',
        'valid_until',
        'status',
        'notes',
        'created_by',
        'last_claim_month',
        'last_claim_year',
        'months_activated'
    ];

    protected $casts = [
        'valid_until' => 'date',
        'last_claim_month' => 'integer',
        'last_claim_year' => 'integer',
        'months_activated' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function bonusRule()
    {
        return $this->belongsTo(BonusRule::class);
    }

    public function claims()
    {
        return $this->hasMany(BonusClaim::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper method
    public function getRemainingHoursAttribute()
    {
        return $this->bonus_hours_total - $this->bonus_hours_used;
    }
}