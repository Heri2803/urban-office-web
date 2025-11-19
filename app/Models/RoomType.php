<?php
// app/Models/RoomType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Relasi ke Room
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Relationship dengan photos
    public function photos()
    {
        return $this->hasMany(ServicePhoto::class);
    }

    // Get primary photo
    public function primaryPhoto()
    {
        return $this->hasOne(ServicePhoto::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query; // Return semua data
    }

    // ✅ SIMPLE: Get all room types (tanpa scope active/ordered)
    public function scopeAvailable($query)
    {
        return $query->orderBy('name'); // Urutkan berdasarkan nama saja
    }

    public function serviceHighlights()
    {
        return $this->belongsToMany(ServiceHighlight::class, 'service_highlight_room_type');
    }
}