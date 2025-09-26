<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Retreat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $retreat;
    public $allParticipants;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, Retreat $retreat, $allParticipants = [])
    {
        $this->booking = $booking;
        $this->retreat = $retreat;
        $this->allParticipants = $allParticipants;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Retreat Booking Confirmation - ' . $this->booking->booking_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'retreat' => $this->retreat,
                'allParticipants' => $this->allParticipants,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
