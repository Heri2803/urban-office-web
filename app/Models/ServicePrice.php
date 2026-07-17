<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasLocationScope;

class ServicePrice extends Model
{
    use HasFactory, SoftDeletes, HasLocationScope;

    protected $fillable = [
        'parent_id', // Tambahkan ini
        'location_id',
        'room_id',
        'room_type_id', 
        'service_category_id',
        'duration_type',
        'duration',
        'base_price',
        'coffee_break_option',
        'coffee_break_price',
        'deposit',
        'request_status', // Tambahkan ini
        'previous_price', // Tambahkan ini
        'request_reason', // Tambahkan ini
        'requested_by',   // Tambahkan ini
        'requested_at',   // Tambahkan ini
        'reviewed_by',    // Tambahkan ini
        'reviewed_at',    // Tambahkan ini
        'requested_by_user_id', // Tambahkan ini
        'reviewed_by_user_id',  // Tambahkan ini
        'deleted_by_user_id',
        'deletion_reason',
        'restored_by_user_id',
        'restored_at',
        'restore_reason',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'coffee_break_price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function locationWithCity()
    {
        return $this->belongsTo(Location::class, 'location_id')->with('city');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    // Self-referencing relationship untuk parent/active price
    public function parent()
    {
        return $this->belongsTo(ServicePrice::class, 'parent_id');
    }

    public function requests()
    {
        return $this->hasMany(ServicePrice::class, 'parent_id')
            ->whereIn('request_status', ['pending', 'rejected']);
    }

    // Scope helpers
    public function scopeActive($query)
    {
        return $query->where('request_status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('request_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('request_status', 'rejected');
    }

    public function scopeInactive($query)
    {
        return $query->where('request_status', 'inactive');
    }

    // Helper methods
    public function isActive()
    {
        return $this->request_status === 'active';
    }

    public function isPending()
    {
        return $this->request_status === 'pending';
    }

    public function isRejected()
    {
        return $this->request_status === 'rejected';
    }

    public function isInactive()
    {
        return $this->request_status === 'inactive';
    }

    /**
     * Format duration untuk display
     * Contoh: "2 jam", "1 bulan", "30 menit"
     */
    public function getDurationDisplayAttribute()
    {
        if (!$this->duration || !$this->duration_type) {
            return 'N/A';
        }
        
        $units = [
            'minutes' => 'menit',
            'hours' => 'jam',
            'days' => 'hari', 
            'months' => 'bulan',
            'years' => 'tahun'
        ];
        
        $unit = $units[$this->duration_type] ?? $this->duration_type;
        return "{$this->duration} {$unit}";
    }
    
    /**
     * Format currency untuk display
     * Contoh: "Rp 150.000", "Rp 2.500.000"
     */
    public function getPriceFormattedAttribute()
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Accessor untuk room_type (lebih clean)
     */
    public function getRoomTypeNameAttribute()
    {
        return $this->roomType->name ?? 'N/A';
    }

     /**
     * Accessor untuk category name
     */
    public function getCategoryNameAttribute()
    {
        return $this->category->name ?? 'General';
    }

    /**
     * Relationships
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
    }
    
    public function restoredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by_user_id');
    }
    
}