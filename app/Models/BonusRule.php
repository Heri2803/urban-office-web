<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonusRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'room_type_trigger',
        'min_gross_amount',
        'bonus_hours',
        'valid_days',
        'is_active',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}