<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceHighlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'show_in_all_services',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_all_services' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ✅ Scope untuk ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // ✅ Scope untuk active highlights
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ✅ Relationships
    public function roomTypes()
    {
        return $this->belongsToMany(
            RoomType::class,
            'service_highlight_room_type',
            'service_highlight_id',
            'room_type_id'
        );
    }

    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'service_highlight_room',
            'service_highlight_id',
            'room_id'
        );
    }
}