<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Retreat;
use App\Services\BrevoWhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationWhatsApp implements ShouldQueue
{
    use Queueable;

    protected $primaryBooking;
    protected $templateId;

    public function __construct(Booking $primaryBooking, int $templateId)
    {
        $this->primaryBooking = $primaryBooking;
        $this->templateId = $templateId;
    }

    public function handle(): void
    {
        try {
            // Validate phone number exists
            if (!$this->primaryBooking->whatsapp_number || !$this->primaryBooking->country_code) {
                Log::warning('WhatsApp number not available for booking', [
                    'booking_id' => $this->primaryBooking->booking_id,
                    'template_id' => $this->templateId,
                ]);
                return;
            }

            $phoneNumber = $this->primaryBooking->country_code.$this->primaryBooking->whatsapp_number;

            // Initialize WhatsApp service
            $whatsappService = new BrevoWhatsAppService();

            // Send WhatsApp message with specified template ID
            $response = $whatsappService->sendTemplateMessage($phoneNumber, $this->templateId);

            Log::channel('booking_confirmations')->info('WhatsApp confirmation sent successfully', [
                'booking_id' => $this->primaryBooking->booking_id,
                'phone' => $phoneNumber,
                'template_id' => $this->templateId,
                'user_name' => $this->primaryBooking->firstname . ' ' . $this->primaryBooking->lastname,
                'response' => $response,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'booking_id' => $this->primaryBooking->booking_id,
                'phone' => $this->primaryBooking->whatsapp_number,
                'template_id' => $this->templateId,
                'user_name' => $this->primaryBooking->firstname . ' ' . $this->primaryBooking->lastname,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to allow queue retry mechanism
        }
    }

}
