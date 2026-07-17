<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocationScope;

class Room extends Model
{
    use HasFactory, HasLocationScope;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'capacity',
        'size_m2',
        'status',
        'location_id',
        'maintenance_start',
        'maintenance_end',   
        'maintenance_reason',
    ];

    // Relasi ke ServicePhoto
    public function servicePhotos()
    {
        return $this->hasMany(ServicePhoto::class);
    }

    // Relasi ke RoomType
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Relasi ke Location
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'room_id');
    }

    /**
     * ✅ TAMBAHKAN: Relationship to ServicePrices
     */
    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class, 'room_id');
    }

    /**
     * ✅ TAMBAHKAN: Check if room supports a room type
     */
    public function supportsRoomType($roomTypeId)
    {
        if (is_null($this->room_type_id)) {
            // Multi-purpose: cek dari service_prices
            return $this->servicePrices()
                ->where('room_type_id', $roomTypeId)
                ->exists();
        }
        
        return $this->room_type_id == $roomTypeId;
    }

    /**
     * ✅ TAMBAHKAN: Check if multi-purpose
     */
    public function isMultiPurpose()
    {
        return is_null($this->room_type_id);
    }

    public function scopeActive($query)
    {
        return $query; // Return semua data
    }

    // ✅ TAMBAHKAN: Relationship untuk service highlights
    public function serviceHighlights()
    {
        return $this->belongsToMany(
            ServiceHighlight::class,
            'service_highlight_room',
            'room_id',
            'service_highlight_id'
        );
    }
}
