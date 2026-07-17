<?php
// app/Models/ServicePhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocationScope;

class ServicePhoto extends Model
{
    use HasFactory, HasLocationScope;

    protected $fillable = [
        'room_id',
        'location_id',
        'room_type_id',
        'filename',
        'original_name',
        'file_path',
        'file_url',
        'file_size',
        'mime_type',
        'caption',
        'is_primary',
        'uploaded_by'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer'
    ];

    //  LOCATION RELATIONSHIP
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Relationship dengan room type
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Room relationship
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Relationship dengan user yang upload
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scope untuk photos berdasarkan room type
    public function scopeForRoomType($query, $roomTypeId)
    {
        return $query->where('room_type_id', $roomTypeId);
    }

    // Scope untuk primary photo
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Set primary photo dan unset lainnya untuk room type yang sama
    public function setAsPrimary()
    {
        \DB::transaction(function () {
            $query = self::where('room_type_id', $this->room_type_id)
                        ->where('location_id', $this->location_id);
            
            // Jika ada room_id, reset hanya untuk room tersebut
            if ($this->room_id) {
                $query->where('room_id', $this->room_id);
            } else {
                // Jika tidak ada room_id, reset untuk semua rooms dengan type tersebut
                $query->whereNull('room_id');
            }
            
            $query->where('is_primary', true)
                  ->update(['is_primary' => false]);

            // Set this photo as primary
            $this->update(['is_primary' => true]);
        });
    }

    // Get formatted file size
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes == 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    // Get human readable upload time
    public function getUploadedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Filter by location
    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }
    
    // Filter by room type and location
    public function scopeForRoomTypeAndLocation($query, $roomTypeId, $locationId)
    {
        return $query->where('room_type_id', $roomTypeId)
                    ->where('location_id', $locationId);
    }
}