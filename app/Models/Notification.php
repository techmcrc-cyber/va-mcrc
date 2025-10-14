<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'need',
        'retreat_id',
        'heading',
        'subject',
        'body',
        'greeting',
        'additional_users_emails',
        'total_recipients',
        'status',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'total_recipients' => 'integer',
    ];

    protected $appends = [
        'recipients_count',
        'formatted_status',
    ];

    /**
     * Get the retreat that owns the notification.
     */
    public function retreat(): BelongsTo
    {
        return $this->belongsTo(Retreat::class);
    }

    /**
     * Get the user who created the notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by need type.
     */
    public function scopeByNeed($query, string $need)
    {
        return $query->where('need', $need);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent notifications.
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get recipients as an array.
     */
    public function getRecipientsArray(): array
    {
        if (empty($this->additional_users_emails)) {
            return [];
        }

        return array_map('trim', explode(',', $this->additional_users_emails));
    }

    /**
     * Mark notification as queued.
     */
    public function markAsQueued(): void
    {
        $this->update(['status' => 'queued']);
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark notification as failed.
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Get recipients count attribute.
     */
    public function getRecipientsCountAttribute(): int
    {
        return $this->total_recipients;
    }

    /**
     * Get formatted status attribute.
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}
