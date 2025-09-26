<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Retreat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public $primaryBooking;
    public $retreat;
    public $cancelledParticipant;
    public $remainingParticipants;
    public $cancellationType;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Booking $primaryBooking, 
        Retreat $retreat, 
        Booking $cancelledParticipant, 
        $remainingParticipants = [], 
        $cancellationType = 'partial'
    ) {
        $this->primaryBooking = $primaryBooking;
        $this->retreat = $retreat;
        $this->cancelledParticipant = $cancelledParticipant;
        $this->remainingParticipants = $remainingParticipants;
        $this->cancellationType = $cancellationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectType = $this->cancellationType === 'full' ? 'Complete Cancellation' : 'Participant Cancellation';
        
        return new Envelope(
            subject: 'Retreat Booking ' . $subjectType . ' - ' . $this->primaryBooking->booking_id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancellation',
            with: [
                'primaryBooking' => $this->primaryBooking,
                'retreat' => $this->retreat,
                'cancelledParticipant' => $this->cancelledParticipant,
                'remainingParticipants' => $this->remainingParticipants,
                'cancellationType' => $this->cancellationType,
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
