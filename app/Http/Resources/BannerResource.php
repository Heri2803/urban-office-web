<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'status' => $this->status,
            'start_date' => $this->start_date->format('M j, Y'),
            'end_date' => $this->end_date->format('M j, Y'),
            'priority' => $this->priority,
            'locations' => $this->locations,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'thumbnail_url' => $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'type' => [
                'id' => $this->type->id,
                'name' => $this->type->name,
                'slug' => $this->type->slug,
            ],
            'metrics' => [
                'view_count' => $this->view_count,
                'click_count' => $this->click_count,
                'usage_count' => $this->usage_count,
            ],
            'is_active' => $this->is_active,
            'days_remaining' => $this->days_remaining,
            'created_at' => $this->created_at->format('M j, Y H:i'),
            'updated_at' => $this->updated_at->format('M j, Y H:i'),
        ];
    }
}