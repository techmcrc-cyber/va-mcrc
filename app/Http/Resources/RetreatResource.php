<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetreatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'timings' => $this->timings,
            'seats' => $this->seats,
            'available_seats' => $this->seats - $this->bookings()->count(),
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'location' => $this->location,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'coordinates' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'criteria' => $this->criteria,
            'criteria_label' => $this->criteria_label,
            'special_remarks' => $this->special_remarks,
            'instructions' => $this->instructions,
            'is_featured' => (bool) $this->is_featured,
            'is_active' => (bool) $this->is_active,
            'is_available' => $this->is_available,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'featured_image' => $this->getFirstMediaUrl('featured_image'),
            'featured_image_thumb' => $this->getFirstMediaUrl('featured_image', 'thumb'),
            'gallery' => $this->whenLoaded('media', function () {
                return $this->getMedia('gallery')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'created_at' => $media->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'updated_by' => new UserResource($this->whenLoaded('updater')),
        ];
    }

    /**
     * Get any additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'available_seats' => $this->seats - $this->bookings()->count(),
                'total_bookings' => $this->bookings()->count(),
                'is_full' => $this->seats <= $this->bookings()->count(),
                'has_discount' => $this->discount_price !== null && $this->discount_price < $this->price,
                'discount_percentage' => $this->discount_price ? round((($this->price - $this->discount_price) / $this->price) * 100) : 0,
            ],
        ];
    }
}
