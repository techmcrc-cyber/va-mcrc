<?php

namespace App\Services;

use App\Models\Retreat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RetreatService
{
    /**
     * Create a new retreat
     *
     * @param array $data
     * @return Retreat
     */
    public function createRetreat(array $data): Retreat
    {
        // Handle featured image
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $data['featured_image_path'] = $this->storeImage($data['featured_image'], 'retreats/featured');
            unset($data['featured_image']);
        }

        // Set created_by and updated_by
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Create the retreat
        $retreat = Retreat::create($data);

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $this->handleGalleryImages($retreat, $data['gallery']);
        }

        return $retreat;
    }

    /**
     * Update an existing retreat
     *
     * @param Retreat $retreat
     * @param array $data
     * @return Retreat
     */
    public function updateRetreat(Retreat $retreat, array $data): Retreat
    {
        // Handle featured image update
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            // Delete old featured image if exists
            if ($retreat->featured_image_path) {
                Storage::delete($retreat->featured_image_path);
            }
            
            $data['featured_image_path'] = $this->storeImage($data['featured_image'], 'retreats/featured');
            unset($data['featured_image']);
        }

        // Set updated_by
        $data['updated_by'] = Auth::id();

        // Update the retreat
        $retreat->update($data);

        // Handle gallery images
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $this->handleGalleryImages($retreat, $data['gallery']);
        }

        return $retreat->fresh();
    }

    /**
     * Delete a retreat and its associated media
     *
     * @param Retreat $retreat
     * @return bool
     */
    public function deleteRetreat(Retreat $retreat): bool
    {
        // Delete featured image if exists
        if ($retreat->featured_image_path) {
            Storage::delete($retreat->featured_image_path);
        }

        // Delete gallery images
        foreach ($retreat->gallery as $image) {
            Storage::delete($image->getPath());
        }

        return $retreat->delete();
    }

    /**
     * Store an uploaded image
     *
     * @param UploadedFile $image
     * @param string $path
     * @return string
     */
    protected function storeImage(UploadedFile $image, string $path): string
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        return $image->storeAs($path, $filename, 'public');
    }

    /**
     * Handle gallery images upload
     *
     * @param Retreat $retreat
     * @param array $images
     * @return void
     */
    protected function handleGalleryImages(Retreat $retreat, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $this->storeImage($image, 'retreats/gallery');
                $retreat->gallery()->create([
                    'path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getClientMimeType(),
                    'size' => $image->getSize(),
                ]);
            }
        }
    }

    /**
     * Get available retreats based on criteria
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableRetreats(array $filters = [])
    {
        $query = Retreat::query()
            ->active()
            ->upcoming()
            ->with(['category', 'media']);

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Order by featured first, then by start date
        $query->orderBy('is_featured', 'desc')
              ->orderBy('start_date');

        return $query->get();
    }
}
