<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoWhatsAppService
{
    protected $apiKey;
    protected $templateId;
    protected $senderNumber;
    protected $baseUrl = 'https://api.brevo.com/v3';

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key');
        
        // Clean sender number - remove + and spaces
        $sender = config('services.brevo.whatsapp.sender');
        $this->senderNumber = str_replace(['+', ' '], '', $sender);

        // Validate configuration
        if (!$this->apiKey) {
            throw new \Exception('Brevo API key not configured. Please set BREVO_API_KEY in .env');
        }

        if (!$this->senderNumber) {
            throw new \Exception('Brevo WhatsApp sender number not configured. Please set BREVO_WHATSAPP_SENDER in .env');
        }
    }

    /*    Verify if a template ID exists and is approved*/

    public function verifyTemplate(int $templateId): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/whatsappCampaigns/template-list');

            if ($response->successful()) {
                $templates = $response->json();
                
                // Find the template by ID
                $template = collect($templates['templates'] ?? [])->firstWhere('id', $templateId);
                
                if ($template) {
                    return [
                        'valid' => true,
                        'template' => $template,
                        'status' => $template['status'] ?? 'Unknown',
                    ];
                }
                
                Log::warning('Template not found', [
                    'template_id' => $templateId,
                    'available_templates' => collect($templates['templates'] ?? [])->pluck('id', 'name'),
                ]);
                
                return [
                    'valid' => false,
                    'error' => 'Template ID not found',
                ];
            }

            throw new \Exception('Failed to fetch templates: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error verifying template', [
                'template_id' => $templateId,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function sendTemplateMessage(string $recipientNumber, int $templateId): array
    {
        try {

            // Verify template before sending (mandatory)

            $verification = $this->verifyTemplate($templateId);
            if (!$verification['valid']) {
                throw new \Exception('Invalid template ID: ' . ($verification['error'] ?? 'Template not found'));
            }
            
            // Check if template is approved

            if (isset($verification['status']) && $verification['status'] !== 'approved') {
                throw new \Exception('Template is not approved. Current status: ' . $verification['status']);
            }
            
            // Clean recipient number - remove + and spaces

            $contactNumber = str_replace(['+', ' '], '', $recipientNumber);
            
            // Prepare the request payload - simplified without params
            
            $payload = [
                'contactNumbers' => [$contactNumber],
                'templateId' => $templateId,
                'senderNumber' => $this->senderNumber,
            ];

            // Make the API request
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/whatsapp/sendMessage', $payload);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData;
            } else {
                // Log error response
                Log::error('Brevo WhatsApp API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'recipient' => $contactNumber,
                    'payload' => $payload,
                ]);

                throw new \Exception('Brevo API error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Exception in BrevoWhatsAppService', [
                'error' => $e->getMessage(),
                'recipient' => $recipientNumber,
                'template_id' => $templateId,
            ]);

            throw $e;
        }
    }

    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->get($this->baseUrl . '/whatsapp/messages/' . $messageId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to get message status: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error fetching WhatsApp message status', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
