<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Retreat;
use App\Mail\BookingConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use Queueable;

    protected $primaryBooking;
    protected $retreat;
    protected $allParticipants;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $primaryBooking, Retreat $retreat, $allParticipants)
    {
        $this->primaryBooking = $primaryBooking;
        $this->retreat = $retreat;
        $this->allParticipants = $allParticipants;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->primaryBooking->email) {
                Mail::to($this->primaryBooking->email)
                    ->send(new BookingConfirmation($this->primaryBooking, $this->retreat, $this->allParticipants));
                
                Log::channel('booking_confirmations')->info('Booking confirmation email sent successfully', [
                    'booking_id' => $this->primaryBooking->booking_id,
                    'email' => $this->primaryBooking->email
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email', [
                'booking_id' => $this->primaryBooking->booking_id,
                'email' => $this->primaryBooking->email,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw to allow queue retry mechanism
        }
    }
}
