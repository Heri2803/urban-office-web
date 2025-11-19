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
        'user_id',
        'city_id',           
        'location_id',       
        'room_id',           
        'city',              
        'room_type',
        'jumlah_orang',
        'booking_date',
        'start_time',
        'paket',
        'bulan',
        'tahun',
        'minggu',            // ← TAMBAHKAN INI
        'jam',
        'hari',              
        'service_category_id',
        'status_pkp',
        'phone',
        'nama_lengkap',
        'email',
        'order_id',
        'gross_amount',
        'deposit',           
        'payment_type',
        'status',
        'snap_token',
        'transaction_time'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'city_id' => 'integer',
        'location_id' => 'integer',
        'room_id' => 'integer',
        'jumlah_orang' => 'integer',
        'booking_date' => 'date',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'minggu' => 'integer',  // ← TAMBAHKAN INI JUGA
        'jam' => 'integer',
        'hari' => 'integer',
        'gross_amount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'is_read' => 'boolean',
        'transaction_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id')
            ->withDefault([
                'name' => 'Tanpa Kategori',
                'description' => null
            ]);
    }

}