<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $heading,
        public string $subject,
        public string $body,
        public ?string $recipientName = null,
        public ?string $greeting = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Process dynamic greeting
        $processedGreeting = $this->processGreeting();
        
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.custom-notification', [
                'heading' => $this->heading,
                'body' => $this->body,
                'recipientName' => $this->recipientName,
                'greeting' => $processedGreeting,
            ]);
    }

    /**
     * Process the greeting with recipient name.
     */
    private function processGreeting(): string
    {
        // If no custom greeting is provided, use default
        if (empty($this->greeting)) {
            $defaultGreeting = $this->recipientName ? "Dear {$this->recipientName}," : "Dear Friend,";
            return "<p>{$defaultGreeting}</p>";
        }

        // Replace {name} placeholder with actual name or "Friend"
        $name = $this->recipientName ?: "Friend";
        $processedGreeting = str_replace('{name}', $name, $this->greeting);
        
        // If the greeting doesn't contain HTML tags, wrap it in a paragraph
        if (strip_tags($processedGreeting) === $processedGreeting) {
            return "<p>{$processedGreeting}</p>";
        }
        
        return $processedGreeting;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'heading' => $this->heading,
            'subject' => $this->subject,
        ];
    }
}
