<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image' => $this->when(
                $this->relationLoaded('media'),
                function () {
                    $media = $this->getFirstMedia('category_image');
                    return $media ? [
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                    ] : null;
                }
            ),
            'is_active' => (bool) $this->is_active,
            'sort_order' => (int) $this->sort_order,
            'parent_id' => $this->parent_id,
            'parent' => new self($this->whenLoaded('parent')),
            'children' => self::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
