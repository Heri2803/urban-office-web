<?php
// app/Models/ServicePhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
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

    // Relationship dengan room type
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
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
            // Unset primary photos untuk room type yang sama
            self::where('room_type_id', $this->room_type_id)
                ->where('is_primary', true)
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
}