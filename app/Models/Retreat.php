<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Retreat extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'start_date',
        'end_date',
        'timings',
        'seats',
        'price',
        'discount_price',
        'location',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'criteria',
        'special_remarks',
        'instructions',
        'whatsapp_channel_link',
        'is_featured',
        'is_active',
        'category_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'seats' => 'integer',
    ];

    protected $appends = ['featured_image_url', 'is_available'];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($retreat) {
            if (auth()->check()) {
                $retreat->created_by = auth()->id();
                $retreat->updated_by = auth()->id();
            }

            if (!$retreat->slug) {
                $retreat->slug = Str::slug($retreat->title . ' ' . now()->format('Y-m-d'));
            }
        });

        static::updating(function ($retreat) {
            if (auth()->check()) {
                $retreat->updated_by = auth()->id();
            }
        });

        static::saving(function ($retreat) {
            if ($retreat->end_date <= $retreat->start_date) {
                throw new \Exception('End date must be after start date');
            }

            if ($retreat->seats < 0) {
                throw new \Exception('Seats cannot be negative');
            }

            if ($retreat->discount_price !== null && $retreat->discount_price >= $retreat->price) {
                throw new \Exception('Discount price must be less than the regular price');
            }
        });
    }

    /**
     * Get the category that owns the retreat.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the bookings for the retreat.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the user who created the retreat.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the retreat.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute()
    {
        $media = $this->getFirstMedia('featured_image');
        return $media ? $media->getUrl() : asset('images/default-retreat.jpg');
    }

    /**
     * Check if the retreat is available for booking.
     */
    public function getIsAvailableAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date->isPast()) {
            return false;
        }

        if ($this->seats <= $this->bookings()->whereIn('status', ['confirmed', 'pending'])->count()) {
            return false;
        }

        return true;
    }

    /**
     * Get the criteria label.
     */
    public function getCriteriaLabelAttribute()
    {
        $criteriaLabels = [
            'male_only' => 'Male Only',
            'female_only' => 'Female Only',
            'priests_only' => 'Priests Only',
            'sisters_only' => 'Sisters Only',
            'youth_only' => 'Youth Only (Age 16-30)',
            'children' => 'Children (Age 15 or below)',
            'no_criteria' => 'No Criteria'
        ];

        return $criteriaLabels[$this->criteria] ?? $this->criteria;
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
             ->singleFile()
             ->registerMediaConversions(function (Media $media) {
                 $this->addMediaConversion('thumb')
                       ->fit(Manipulations::FIT_CROP, 150, 150)
                       ->nonQueued();
                 
                 $this->addMediaConversion('banner')
                       ->fit(Manipulations::FIT_CROP, 1200, 400)
                       ->nonQueued();
             });

        $this->addMediaCollection('gallery');
    }

    /**
     * Scope a query to only include active retreats.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured retreats.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include upcoming retreats.
     * Shows retreats that haven't ended yet (includes ongoing retreats).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include ongoing retreats.
     */
    public function scopeOngoing($query)
    {
        $now = now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
