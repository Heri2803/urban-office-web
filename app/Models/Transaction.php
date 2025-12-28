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
        'minggu',            
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
        'lunch_total',       // ← TAMBAHKAN INI
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
        'booking_date' => 'datetime',
        'bulan' => 'integer',
        'tahun' => 'integer',
        'minggu' => 'integer',  
        'jam' => 'integer',
        'hari' => 'integer',
        'gross_amount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'lunch_total' => 'decimal:2',  // ← TAMBAHKAN INI
        'is_read' => 'boolean',
        'transaction_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ✅ HANYA INI YANG DIPERLUKAN untuk All Bookings
    protected $appends = ['duration_text', 'participants_text'];
    
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

    // ← TAMBAHKAN RELATIONSHIP UNTUK LUNCH
    /**
     * Get the lunch items for the transaction.
     */
    public function lunches()
    {
        return $this->hasMany(TransactionLunch::class);
    }

    /**
     * Calculate total lunch amount.
     */
    public function calculateLunchTotal()
    {
        return $this->lunches->sum('subtotal');
    }

    /**
     * Get total amount including lunch.
     */
    public function getTotalAmountAttribute()
    {
        return $this->gross_amount + $this->lunch_total;
    }

    /**
     * Boot method for auto-updating lunch total.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update lunch_total when lunches are saved/deleted
        static::saved(function ($model) {
            $lunchTotal = $model->calculateLunchTotal();
            if ($model->lunch_total != $lunchTotal) {
                $model->updateQuietly(['lunch_total' => $lunchTotal]);
            }
        });

        // Delete related lunch items when transaction is deleted
        static::deleting(function ($model) {
            $model->lunches()->delete();
        });
    }


    /**
     * ✅ SCOPES YANG DIPERLUKAN UNTUK FILTERING:
     */
    public function scopeSettlement($query)
    {
        return $query->where('status', 'settlement');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeExpire($query)
    {
        return $query->where('status', 'expire');
    }
    
    public function scopeByServiceType($query, $serviceType)
    {
        if ($serviceType) {
            return $query->where('room_type', $serviceType);
        }
        return $query;
    }
    
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('booking_date', [$startDate, $endDate]);
        }
        return $query;
    }
    
    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where(function($q) use ($searchTerm) {
                $q->where('order_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }
        return $query;
    }

    /**
     * ✅ ACCESSORS YANG DIPERLUKAN UNTUK FRONTEND:
     */
    public function getDurationTextAttribute()
    {
        if ($this->jam) return $this->jam . ' Hours';
        if ($this->hari) return $this->hari . ' Days';
        if ($this->minggu) return $this->minggu . ' Weeks';
        if ($this->bulan) return $this->bulan . ' Months';
        if ($this->tahun) return $this->tahun . ' Years';
        return 'Custom Duration';
    }
    
    public function getParticipantsTextAttribute()
    {
        return $this->jumlah_orang 
            ? $this->jumlah_orang . ' people' 
            : 'Not specified';
    }

    public function getBookingDateOnlyAttribute()
    {
        return $this->booking_date->format('Y-m-d'); // Untuk All Bookings
    }

    public function getBookingDateTimeAttribute()
    {
        return $this->booking_date->format('Y-m-d H:i:s'); // Untuk Room Assignment
    }
    
    public function getBookingTimeFormattedAttribute()
    {
        if ($this->start_time && $this->jam) {
            return $this->start_time . ' (' . $this->jam . ' hours)';
        }
        
        if ($this->start_time) {
            return $this->start_time;
        }
        
        if ($this->paket === 'daily' && $this->hari) {
            return 'Full Day (' . $this->hari . ' days)';
        }
        
        return 'Flexible';
    }

    /**
     * ✅ METHOD BUSINESS LOGIC YANG DIPERLUKAN:
     */
    public function isSettlement()
    {
        return $this->status === 'settlement';
    }
    
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    public function isExpire()
    {
        return $this->status === 'expire';
    }
    
    public function getServiceTypeSlugAttribute()
    {
        $mapping = [
            'Meeting Room' => 'meeting',
            'Private Office' => 'private',
            'Sharing Room' => 'sharing', 
            'Virtual Office' => 'virtual',
            'Coworking Space' => 'coworking',
            'Event Space' => 'event'
        ];
        
        return $mapping[$this->room_type] ?? strtolower(str_replace(' ', '_', $this->room_type));
    }
}