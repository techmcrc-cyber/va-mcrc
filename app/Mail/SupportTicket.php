<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class SupportTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @param array $ticket
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyToAddress = env('MAIL_REPLY_TO_ADDRESS', config('mail.from.address'));
        $replyToName = env('MAIL_REPLY_TO_NAME', config('mail.from.name'));
        
        return new Envelope(
            subject: "[Support Ticket] {$this->ticket['subject']}",
            replyTo: [
                new Address($replyToAddress, $replyToName)
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.support-ticket',
            with: [
                'ticket' => $this->ticket,
            ]
        );
    }
}
