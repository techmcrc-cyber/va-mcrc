<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Notifications\CustomEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class SendNotificationToRecipients implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification,
        public array $recipients
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to processing
            $this->notification->update(['status' => 'processing']);

            // Send notification to each recipient
            $customNotification = new CustomEmailNotification(
                $this->notification->heading,
                $this->notification->subject,
                $this->notification->body
            );

            foreach ($this->recipients as $email) {
                try {
                    NotificationFacade::route('mail', $email)
                        ->notify($customNotification);
                } catch (\Exception $e) {
                    Log::error('Failed to send notification to ' . $email, [
                        'notification_id' => $this->notification->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Mark as sent
            $this->notification->markAsSent();

            Log::info('Notification sent successfully', [
                'notification_id' => $this->notification->id,
                'recipients_count' => count($this->recipients),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process notification', [
                'notification_id' => $this->notification->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->notification->markAsFailed();

        Log::error('Notification job failed', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
