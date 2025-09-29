<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',           // ✅ Tambahkan ini!
        'city',
        'room_type',
        'jumlah_orang',
        'booking_date',
        'paket',
        'bulan',
        'tahun',
        'jam',
        'status_pkp',
        'phone',
        'nama_lengkap',
        'email',
        'order_id',
        'gross_amount',
        'payment_type',
        'status',
        'snap_token',
        'transaction_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
        'transaction_time' => 'datetime',
        'gross_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}