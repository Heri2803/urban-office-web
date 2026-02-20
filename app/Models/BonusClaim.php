<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusClaim extends Model
{
    protected $fillable = [
        'user_bonus_id',
        'meeting_transaction_id',
        'admin_id',
        'hours_used',
        'claim_date',
        'claim_start_time',
        'status',
        'notes'
    ];

    public function userBonus()
    {
        return $this->belongsTo(UserBonus::class);
    }

    public function meetingTransaction()
    {
        return $this->belongsTo(Transaction::class, 'meeting_transaction_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}