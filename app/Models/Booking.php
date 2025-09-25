<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{

    protected $fillable = [
        'booking_id',
        'retreat_id',
        'firstname',
        'lastname',
        'whatsapp_number',
        'age',
        'email',
        'address',
        'gender',
        'city',
        'state',
        'diocese',
        'parish',
        'congregation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'additional_participants',
        'special_remarks',
        'flag',
        'participant_number',
        'created_by',
        'updated_by',
        'is_active',
    ];

    protected $casts = [
        'age' => 'integer',
        'additional_participants' => 'integer',
        'participant_number' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the retreat that owns the booking.
     */
    public function retreat(): BelongsTo
    {
        return $this->belongsTo(Retreat::class);
    }

    /**
     * Get the user who created the booking.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the booking.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all active bookings with the same booking ID (primary + additional participants).
     */
    public function allParticipants()
    {
        return self::where('booking_id', $this->booking_id)
            ->where('is_active', true)
            ->orderBy('participant_number')
            ->get();
    }

    /**
     * Generate the next booking ID (RB1, RB2, etc.).
     */
    public static function generateBookingId(): string
    {
        $lastBooking = self::where('booking_id', 'like', 'RB%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBooking) {
            return 'RB1';
        }

        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastBooking->booking_id);
        return 'RB' . ($lastNumber + 1);
    }
    
    /**
     * Scope to get only active bookings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the user has attended a retreat in the past year based on exact match.
     * Checks combination of firstname, lastname, whatsapp_number within the past year.
     * Excludes the current booking if booking ID is provided.
     */
    public static function hasAttendedInPastYear(string $whatsappNumber, string $firstName, string $lastName, string $currentBookingId = null): bool
    {
        $query = self::where('whatsapp_number', $whatsappNumber)
            ->where('firstname', $firstName)
            ->where('lastname', $lastName)
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subYear());
        
        // Exclude the current booking if provided (for updates)
        if ($currentBookingId) {
            $query->where('booking_id', '!=', $currentBookingId);
        }
        
        return $query->exists();
    }

    /**
     * Check if user meets retreat criteria.
     */
    public function meetsRetreatCriteria(): bool
    {
        $retreat = $this->retreat;
        
        if (!$retreat || $retreat->criteria === 'no_criteria') {
            return true;
        }

        $criteriaCheck = [
            'male_only' => $this->gender === 'male',
            'female_only' => $this->gender === 'female',
            'priests_only' => $this->congregation !== null && !empty(trim($this->congregation)),
            'sisters_only' => $this->gender === 'female' && $this->congregation !== null && !empty(trim($this->congregation)),
            'youth_only' => $this->age >= 16 && $this->age <= 30, // Youth: Age 16-30
            'children' => $this->age <= 15, // Children: Age 15 or below
        ];

        return $criteriaCheck[$retreat->criteria] ?? false;
    }
}
