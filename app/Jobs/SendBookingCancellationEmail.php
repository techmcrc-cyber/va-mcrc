<?php

namespace App\Jobs;

use App\Mail\BookingCancellation;
use App\Models\Booking;
use App\Models\Retreat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingCancellationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $primaryBooking;
    public $retreat;
    public $cancelledParticipant;
    public $remainingParticipants;
    public $cancellationType;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Booking $primaryBooking,
        Retreat $retreat,
        Booking $cancelledParticipant,
        $remainingParticipants = [],
        string $cancellationType = 'partial'
    ) {
        $this->primaryBooking = $primaryBooking;
        $this->retreat = $retreat;
        $this->cancelledParticipant = $cancelledParticipant;
        $this->remainingParticipants = $remainingParticipants;
        $this->cancellationType = $cancellationType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->primaryBooking->email)
                ->send(new BookingCancellation(
                    $this->primaryBooking,
                    $this->retreat,
                    $this->cancelledParticipant,
                    $this->remainingParticipants,
                    $this->cancellationType
                ));

            Log::info('Cancellation email sent successfully', [
                'booking_id' => $this->primaryBooking->booking_id,
                'primary_email' => $this->primaryBooking->email,
                'cancellation_type' => $this->cancellationType
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email', [
                'booking_id' => $this->primaryBooking->booking_id,
                'primary_email' => $this->primaryBooking->email,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Cancellation email job failed', [
            'booking_id' => $this->primaryBooking->booking_id,
            'primary_email' => $this->primaryBooking->email,
            'error' => $exception->getMessage()
        ]);
    }
}
