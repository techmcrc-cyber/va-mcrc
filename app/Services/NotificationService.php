<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;

class NotificationService
{
    /**
     * Create a new notification.
     */
    public function createNotification(array $data): Notification
    {
        $data['total_recipients'] = $this->calculateTotalRecipients($data);
        $data['created_by'] = auth()->id();

        return Notification::create($data);
    }

    /**
     * Get all recipients for a notification.
     */
    public function getRecipients(Notification $notification): array
    {
        $recipients = [];

        // Get retreat participant emails if retreat notification
        if ($notification->need === 'retreat' && $notification->retreat_id) {
            $recipients = array_merge(
                $recipients,
                $this->getRetreatParticipantEmails($notification->retreat_id)
            );
        }

        // Get additional emails
        if ($notification->additional_users_emails) {
            $additionalEmails = $this->parseEmails($notification->additional_users_emails);
            $recipients = array_merge($recipients, $additionalEmails);
        }

        // Remove duplicates and return
        return array_unique($recipients);
    }

    /**
     * Parse comma-separated emails and validate.
     */
    public function parseEmails(string $emailString): array
    {
        if (empty(trim($emailString))) {
            return [];
        }

        $emails = array_map('trim', explode(',', $emailString));
        $validEmails = [];

        foreach ($emails as $email) {
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }

        return $validEmails;
    }

    /**
     * Get retreat participant emails.
     */
    private function getRetreatParticipantEmails(int $retreatId): array
    {
        return Booking::where('retreat_id', $retreatId)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter(function ($email) {
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            })
            ->toArray();
    }

    /**
     * Calculate total recipients.
     */
    private function calculateTotalRecipients(array $data): int
    {
        $count = 0;

        // Count retreat participants
        if (isset($data['need']) && $data['need'] === 'retreat' && isset($data['retreat_id'])) {
            $count += count($this->getRetreatParticipantEmails($data['retreat_id']));
        }

        // Count additional emails
        if (isset($data['additional_users_emails']) && !empty($data['additional_users_emails'])) {
            $count += count($this->parseEmails($data['additional_users_emails']));
        }

        return $count;
    }

    /**
     * Dispatch notification to queue.
     */
    public function dispatchNotification(Notification $notification): void
    {
        $recipients = $this->getRecipients($notification);

        if (empty($recipients)) {
            $notification->markAsFailed();
            return;
        }

        // Mark as queued
        $notification->markAsQueued();

        // Dispatch job (will be created in task 6)
        \App\Jobs\SendNotificationToRecipients::dispatch($notification, $recipients);
    }
}
