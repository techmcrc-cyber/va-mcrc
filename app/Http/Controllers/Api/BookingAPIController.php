<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Retreat;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;

class BookingAPIController extends BaseAPIController
{
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate request structure
            $initialValidator = Validator::make($request->all(), [
                'retreat_id' => 'required|integer|exists:retreats,id',
                'participants' => 'required|array|min:1|max:4',
            ]);

            if ($initialValidator->fails()) {
                return $this->sendValidationError($initialValidator->errors());
            }

            $retreat = Retreat::with(['bookings' => function($query) {
                    $query->active();
                }])->where('id', $request->retreat_id)
                  ->active()
                  ->first();

            if (!$retreat) {
                return $this->sendNotFound('Retreat not found or inactive');
            }

            if ($retreat->end_date->toDateString() < now()->toDateString()) {
                return $this->sendError('This retreat has already ended', 'RETREAT_PAST');
            }

            $currentBookings = $retreat->bookings->count();
            $requestedSeats = count($request->participants);
            $availableSeats = $retreat->seats - $currentBookings;

            if ($requestedSeats > $availableSeats) {
                return $this->sendError(
                    "Only {$availableSeats} seats available. You requested {$requestedSeats} seats.",
                    'INSUFFICIENT_SEATS'
                );
            }

            $validationErrors = [];
            $participants = $request->participants;

            foreach ($participants as $index => $participant) {
                $participantRules = $this->getParticipantValidationRules($retreat);
                $validator = Validator::make($participant, $participantRules, [
                    'whatsapp_number.digits' => 'WhatsApp number must be exactly 10 digits.',
                    'whatsapp_number.numeric' => 'WhatsApp number must contain only digits.',
                    'congregation.required' => 'Congregation is required for this retreat type.',
                ]);

                if ($validator->fails()) {
                    $validationErrors["participant_{$index}"] = $validator->errors();
                }
            }

            if (!empty($validationErrors)) {
                return $this->sendValidationError($validationErrors, 'Participant validation failed');
            }

            // Validate business rules (criteria + recurrent booking) with STRICT mode
            try {
                $this->validateBusinessRules($participants, $retreat);
            } catch (\Exception $e) {
                return $this->sendError($e->getMessage(), 'VALIDATION_FAILED', 400);
            }

            DB::beginTransaction();

            try {

                $bookingId = Booking::generateBookingId();
                $allBookings = [];
                $primaryBooking = null;

                foreach ($participants as $index => $participantData) {
                    $serialNumber = $index + 1;

                    $booking = Booking::create([
                        'booking_id' => $bookingId,
                        'retreat_id' => $retreat->id,
                        'firstname' => $participantData['firstname'],
                        'lastname' => $participantData['lastname'],
                        'whatsapp_number' => $participantData['whatsapp_number'],
                        'age' => $participantData['age'],
                        'email' => $participantData['email'],
                        'address' => $participantData['address'],
                        'gender' => $participantData['gender'],
                        'married' => $participantData['married'] ?? null,
                        'city' => $participantData['city'],
                        'state' => $participantData['state'],
                        'diocese' => $participantData['diocese'] ?? null,
                        'parish' => $participantData['parish'] ?? null,
                        'congregation' => $participantData['congregation'] ?? null,
                        'emergency_contact_name' => $participantData['emergency_contact_name'],
                        'emergency_contact_phone' => $participantData['emergency_contact_phone'],
                        'additional_participants' => count($participants) - 1, // For primary participant only
                        'special_remarks' => $participantData['special_remarks'] ?? null,
                        'participant_number' => $serialNumber,
                        'is_active' => true,
                        'flag' => null, // No flags - strict validation blocks invalid bookings
                        'created_by' => null, // API bookings don't have user context
                        'updated_by' => null,
                    ]);

                    $allBookings[] = $booking;

                    if ($serialNumber === 1) {
                        $primaryBooking = $booking;
                    }
                }

                // Queue confirmation email
                if ($primaryBooking && $primaryBooking->email) {
                    \App\Jobs\SendBookingConfirmationEmail::dispatch($primaryBooking, $retreat, collect($allBookings));
                }

                DB::commit();

                $responseData = [
                    'booking_id' => $bookingId,
                    'retreat' => [
                        'id' => $retreat->id,
                        'name' => $retreat->title,
                        'start_date' => $retreat->start_date->format('Y-m-d'),
                        'end_date' => $retreat->end_date->format('Y-m-d'),
                    ],
                    'participants' => collect($allBookings)->map(function ($booking) {
                        return [
                            'serial_number' => $booking->participant_number,
                            'name' => $booking->firstname . ' ' . $booking->lastname,
                            'email' => $booking->email,
                            'whatsapp_number' => $booking->whatsapp_number,
                            'role' => $booking->participant_number === 1 ? 'primary' : 'secondary',
                        ];
                    }),
                    'total_participants' => count($allBookings),
                    'remarks' => 'Booking confirmed successfully. Confirmation email sent to primary participant.',
                ];

                return $this->sendCreated($responseData, 'Booking created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error during booking creation: ' . $e->getMessage());
                return $this->sendServerError('Failed to create booking due to database error');
            }

        } catch (\Exception $e) {
            \Log::error('API - Failed to create booking: ' . $e->getMessage());
            return $this->sendServerError('Failed to create booking');
        }
    }

    private function getParticipantValidationRules(Retreat $retreat): array
    {

        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|numeric|digits:10',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'gender' => 'required|in:male,female,other',
            'married' => 'nullable|in:yes,no',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'special_remarks' => 'nullable|string|max:1000',
        ];

        if (in_array($retreat->criteria, ['priests_only', 'sisters_only'])) {
            $rules['congregation'] = 'required|string|max:255';
        }

        return $rules;
    }

    private function validateBusinessRules(array $participants, Retreat $retreat): void
    {
        $validationService = new \App\Services\CriteriaValidationService();
        $errorMessages = [];

        // Validate each participant with STRICT mode
        foreach ($participants as $index => $participant) {
            $validation = $validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                true // Strict mode - blocks both criteria failures AND recurrent bookings
            );

            // If validation fails (criteria OR recurrent booking), collect error
            if (!$validation['valid']) {
                $participantName = ($participant['firstname'] ?? '') . ' ' . ($participant['lastname'] ?? '');
                $errorMessages[] = "Participant " . ($index + 1) . " ({$participantName}): " . implode(', ', $validation['messages']);
            }
        }

        // If any participant fails validation, throw error with all messages
        if (!empty($errorMessages)) {
            throw new \Exception(implode('; ', $errorMessages));
        }
    }
    
    public function show(Request $request): JsonResponse
    {
        try {

            $bookingId = $request->header('booking-id') ?? $request->query('booking_id');
            $whatsappNumber = $request->header('whatsapp-number') ?? $request->query('whatsapp_number');
            $participantNumber = $request->header('participant-number') ?? $request->query('participant_number');

            $validator = Validator::make([
                'booking_id' => $bookingId,
                'whatsapp_number' => $whatsappNumber,
                'participant_number' => $participantNumber,
            ], [
                'booking_id' => 'required|string',
                'whatsapp_number' => 'required|numeric|digits:10',
                'participant_number' => 'nullable|integer|min:1|max:4',
            ], [
                'booking_id.required' => 'Booking ID is required in headers or query parameters',
                'whatsapp_number.required' => 'WhatsApp number is required in headers or query parameters',
                'whatsapp_number.digits' => 'WhatsApp number must be exactly 10 digits',
                'participant_number.integer' => 'Participant number must be a valid integer',
                'participant_number.min' => 'Participant number must be at least 1',
                'participant_number.max' => 'Participant number cannot exceed 4',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            // Build query to find the specific participant
            $query = Booking::with(['retreat'])
                ->where('booking_id', $bookingId)
                ->where('whatsapp_number', $whatsappNumber)
                ->where('is_active', true);
            
            // If participant_number is provided, use it for precise identification
            // This handles cases where multiple participants share the same whatsapp number
            if ($participantNumber) {
                $query->where('participant_number', $participantNumber);
            }
            
            $participant = $query->first();

            if (!$participant) {
                $errorMessage = $participantNumber 
                    ? 'Unable to retrieve booking information for the specified Booking ID, WhatsApp number, and participant number'
                    : 'Unable to retrieve booking information for the specified Booking ID and WhatsApp number';
                
                return $this->sendError($errorMessage, 'BOOKING_NOT_FOUND', 400);
            }
            
            // Check if there are multiple participants with the same whatsapp number
            $duplicateCheck = Booking::where('booking_id', $bookingId)
                ->where('whatsapp_number', $whatsappNumber)
                ->where('is_active', true)
                ->count();
            // If multiple participants share the same number and no participant_number was provided,
            // return a warning in the response
            $multipleParticipantsWarning = null;
            if ($duplicateCheck > 1 && !$participantNumber) {
                $multipleParticipantsWarning = 'Multiple participants found with this WhatsApp number. Showing details for participant #' . $participant->participant_number . '. To view a specific participant, include participant_number in your request.';
            }

           //dd($duplicateCheck); 
            // Store booking details in session for future use (e.g., cancellation)
            $sessionId = $request->input('session_id');
            if ($sessionId) {
                \Cache::put("session:{$sessionId}:booking_context", [
                    'booking_id' => $bookingId,
                    'whatsapp_number' => $whatsappNumber,
                    'participant_number' => $participant->participant_number,
                    'stored_at' => now()
                ], 60 * 24); // 24 hours
            }

            $allParticipants = Booking::where('booking_id', $bookingId)
                ->where('is_active', true)
                ->orderBy('participant_number')
                ->get();

            $retreat = $participant->retreat;

            if (!$retreat || !$retreat->is_active) {
                return $this->sendError('Associated retreat is no longer available', 'RETREAT_UNAVAILABLE');
            }

            $participantRole = $participant->participant_number === 1 ? 'primary' : 'secondary';
            $primaryBooking = $allParticipants->where('participant_number', 1)->first();

            $today = now()->toDateString();
            $retreatStatus = 'upcoming';
            if ($retreat->end_date->toDateString() < $today) {
                $retreatStatus = 'completed';
            } elseif ($retreat->start_date->toDateString() <= $today && $retreat->end_date->toDateString() >= $today) {
                $retreatStatus = 'ongoing';
            }

            // Prepare comprehensive response
            $bookingDetails = [
                'booking_id' => $participant->booking_id,
                'booking_date' => $participant->created_at->format('Y-m-d H:i:s'),
                'status' => 'confirmed', // Since we only show active bookings
                'retreat_status' => $retreatStatus,
                'current_user_role' => $participantRole, // Simple role indicator

                // Retreat Information
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'description' => $retreat->description,
                    'short_description' => $retreat->short_description,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                    'start_datetime' => $retreat->start_date->format('Y-m-d H:i:s'),
                    'end_datetime' => $retreat->end_date->format('Y-m-d H:i:s'),
                    'timings' => $retreat->timings,
                    'criteria' => [
                        'type' => $retreat->criteria,
                        'label' => $retreat->criteria_label,
                    ],
                    'instructions' => $retreat->instructions,
                    'special_remarks' => $retreat->special_remarks,
                ],

                // Primary Participant Details (for reference)
                'primary_participant' => $primaryBooking ? [
                    'serial_number' => $primaryBooking->participant_number,
                    'firstname' => $primaryBooking->firstname,
                    'lastname' => $primaryBooking->lastname,
                    'full_name' => $primaryBooking->firstname . ' ' . $primaryBooking->lastname,
                    'email' => $primaryBooking->email,
                    'whatsapp_number' => $primaryBooking->whatsapp_number,
                    'age' => $primaryBooking->age,
                    'gender' => ucfirst($primaryBooking->gender),
                ] : null,

                // All Participants (Simplified)
                'participants' => $allParticipants->map(function ($p) {
                    return [
                        'serial_number' => $p->participant_number,
                        'firstname' => $p->firstname,
                        'lastname' => $p->lastname,
                        'full_name' => $p->firstname . ' ' . $p->lastname,
                        'email' => $p->email,
                        'whatsapp_number' => $p->whatsapp_number,
                        'age' => $p->age,
                        'gender' => ucfirst($p->gender),
                        'role' => $p->participant_number === 1 ? 'primary' : 'secondary',
                    ];
                })->values(),

                // Booking Summary
                'summary' => [
                    'total_participants' => $allParticipants->count(),
                    'additional_participants' => $primaryBooking ? $primaryBooking->additional_participants : 0,
                    'special_remarks' => $participant->special_remarks,
                ],

                // Important Dates and Status
                'important_info' => [
                    'days_until_retreat' => $retreat->start_date->isFuture() ?
                        $now->diffInDays($retreat->start_date, false) : null,
                    'is_cancellable' => $retreat->start_date->isFuture() &&
                        $retreat->start_date->diffInDays($now) > 1, // Can cancel if more than 1 day before
                    'check_in_time' => $retreat->start_date->format('M d, Y \a\t g:i A'),
                    'check_out_time' => $retreat->end_date->format('M d, Y \a\t g:i A'),
                    'duration_days' => $retreat->start_date->diffInDays($retreat->end_date) + 1,
                ],
            ];
            
            // Add warning if multiple participants share the same whatsapp number
            if ($multipleParticipantsWarning) {
                $bookingDetails['warning'] = $multipleParticipantsWarning;
            }

            return $this->sendResponse($bookingDetails, 'Booking details retrieved successfully');

        } catch (\Exception $e) {
            \Log::error('API - Failed to retrieve booking details: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve booking details');
        }
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            // Get session ID and retrieve booking context
            $sessionId = $request->input('session_id');

            if (!$sessionId) {
                return $this->sendError(
                    'Session ID is required. Please retrieve booking details first using GET /api/bookings',
                    'SESSION_REQUIRED',
                    400
                );
            }
            
            $sessionContext = \Cache::get("session:{$sessionId}:booking_context");
            
            if (!$sessionContext) {
                return $this->sendError(
                    'No booking context found in session. Please retrieve booking details first using GET /api/bookings',
                    'SESSION_CONTEXT_MISSING',
                    400
                );
            }

            // Get whatsapp_number and participant_number ONLY from session
            $whatsappNumber = $sessionContext['whatsapp_number'];
            $currentUserParticipantNumber = $sessionContext['participant_number'];
            $sessionBookingId = $sessionContext['booking_id'];
            
            // Validate that the booking_id in URL matches the one in session
            if ($id !== $sessionBookingId) {
                return $this->sendError(
                    'Booking ID mismatch. You can only cancel bookings from your current session.',
                    'BOOKING_ID_MISMATCH',
                    403
                );
            }

            // Get serial_number from request body (REQUIRED)
            $bookingId = $id;
            $serialNumber = $request->input('serial_number');
            
            // Validate serial_number is provided
            if (!$serialNumber) {
                return $this->sendError(
                    'The serial_number field is required in the request body',
                    'SERIAL_NUMBER_REQUIRED',
                    400
                );
            }
            
            // Validate serial_number format
            if (!is_numeric($serialNumber) || $serialNumber < 1 || $serialNumber > 4) {
                return $this->sendError(
                    'Invalid serial number. Must be between 1 and 4.',
                    'INVALID_SERIAL_NUMBER',
                    400
                );
            }
            
            $serialNumber = (int) $serialNumber;

            // Determine the current user's role using session data
            $currentUserParticipant = Booking::with(['retreat'])
                ->where('booking_id', $bookingId)
                ->where('whatsapp_number', $whatsappNumber)
                ->where('participant_number', $currentUserParticipantNumber)
                ->where('is_active', true)
                ->first();

            if (!$currentUserParticipant) {
                return $this->sendError(
                    'Unable to find your participant record. Please verify your booking details.',
                    'USER_NOT_FOUND',
                    400
                );
            }

            $currentUserRole = $currentUserParticipant->participant_number === 1 ? 'primary' : 'secondary';

            // Find the participant to cancel
            $participantToCancel = Booking::where('booking_id', $bookingId)
                ->where('participant_number', $serialNumber)
                ->where('is_active', true)
                ->first();

            if (!$participantToCancel) {
                return $this->sendError(
                    'Unable to cancel booking for the specified participant serial number',
                    'PARTICIPANT_NOT_FOUND',
                    400
                );
            }

            // Get retreat details
            $retreat = $participantToCancel->retreat;
            if (!$retreat) {
                return $this->sendError('Associated retreat not found', 'RETREAT_NOT_FOUND');
            }

            // Check if retreat is cancellable (not already started)
            if ($retreat->start_date->isPast()) {
                return $this->sendError(
                    'Cannot cancel booking for a retreat that has already started',
                    'RETREAT_ALREADY_STARTED'
                );
            }

            // Get all participants for this booking
            $allParticipants = Booking::where('booking_id', $bookingId)
                ->where('is_active', true)
                ->orderBy('participant_number')
                ->get();

            if ($allParticipants->isEmpty()) {
                return $this->sendError('No active participants found for this booking', 'NO_PARTICIPANTS');
            }

            // Role-based cancellation logic
            if ($currentUserRole === 'primary') {
                // Primary can cancel: entire booking, own participation (cancels all), or any secondary
                return $this->handlePrimaryCancellation($participantToCancel, $allParticipants, $retreat, $serialNumber);
            } else {
                // Secondary can only cancel their own participation
                if ($serialNumber !== $currentUserParticipant->participant_number) {
                    return $this->sendError(
                        'As a secondary participant, you can only cancel your own participation',
                        'UNAUTHORIZED_CANCELLATION',
                        403
                    );
                }
                return $this->handleSecondaryCancellation($participantToCancel, $allParticipants, $retreat);
            }

        } catch (\Exception $e) {
            \Log::error('API - Failed to cancel booking participant: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel booking participant');
        }
    }

    /**
     * Handle cancellation when current user is primary participant
     */
    private function handlePrimaryCancellation($participantToCancel, $allParticipants, $retreat, $serialNumber): JsonResponse
    {
        $primaryBooking = $allParticipants->where('participant_number', 1)->first();

        // If primary is cancelling their own participation, it cancels the entire booking
        if ($serialNumber === 1) {
            return $this->processCompleteCancellation($participantToCancel, $retreat, $allParticipants);
        }

        // If primary is cancelling any secondary participant, do partial cancellation
        DB::beginTransaction();

        try {
            // Mark participant as inactive (soft cancel)
            $participantToCancel->update([
                'is_active' => false,
                'updated_by' => null,
            ]);

            // Get remaining active participants
            $remainingParticipants = $allParticipants->where('id', '!=', $participantToCancel->id);

            // Update additional_participants count for primary booking
            if ($primaryBooking && $primaryBooking->is_active) {
                $activeCount = $remainingParticipants->count();
                $primaryBooking->update([
                    'additional_participants' => max(0, $activeCount - 1),
                ]);
            }

            // Send cancellation confirmation email to primary participant
            try {
                Mail::to($primaryBooking->email)
                    ->send(new BookingCancellation(
                        $primaryBooking,
                        $retreat,
                        $participantToCancel,
                        $remainingParticipants->values(),
                        'partial'
                    ));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation confirmation email: ' . $e->getMessage());
            }

            DB::commit();

            // Prepare response
            $responseData = [
                'booking_id' => $participantToCancel->booking_id,
                'cancelled_participant' => [
                    'serial_number' => $participantToCancel->participant_number,
                    'name' => $participantToCancel->firstname . ' ' . $participantToCancel->lastname,
                    'email' => $participantToCancel->email,
                    'whatsapp_number' => $participantToCancel->whatsapp_number,
                ],
                'remaining_participants' => $remainingParticipants->map(function ($participant) {
                    return [
                        'serial_number' => $participant->participant_number,
                        'name' => $participant->firstname . ' ' . $participant->lastname,
                        'email' => $participant->email,
                        'whatsapp_number' => $participant->whatsapp_number,
                        'role' => $participant->participant_number === 1 ? 'primary' : 'secondary',
                    ];
                })->values(),
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                ],
                'cancellation_type' => 'partial',
                'total_remaining' => $remainingParticipants->count(),
                'message' => 'Participant cancelled successfully by primary participant. Confirmation email sent to primary participant.',
            ];

            return $this->sendResponse($responseData, 'Booking participant cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Database error during primary cancellation: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel participant due to database error');
        }
    }

    /**
     * Handle cancellation when current user is secondary participant
     */
    private function handleSecondaryCancellation($participantToCancel, $allParticipants, $retreat): JsonResponse
    {
        $primaryBooking = $allParticipants->where('participant_number', 1)->first();

        DB::beginTransaction();

        try {
            // Mark participant as inactive (soft cancel)
            $participantToCancel->update([
                'is_active' => false,
                'updated_by' => null,
            ]);

            // Get remaining active participants
            $remainingParticipants = $allParticipants->where('id', '!=', $participantToCancel->id);

            // Update additional_participants count for primary booking
            if ($primaryBooking && $primaryBooking->is_active) {
                $activeCount = $remainingParticipants->count();
                $primaryBooking->update([
                    'additional_participants' => max(0, $activeCount - 1),
                ]);
            }

            // Send cancellation confirmation email to primary participant
            try {
                Mail::to($primaryBooking->email)
                    ->send(new BookingCancellation(
                        $primaryBooking,
                        $retreat,
                        $participantToCancel,
                        $remainingParticipants->values(),
                        'partial'
                    ));
            } catch (\Exception $e) {
                \Log::error('Failed to send cancellation confirmation email: ' . $e->getMessage());
            }

            DB::commit();

            // Prepare response
            $responseData = [
                'booking_id' => $participantToCancel->booking_id,
                'cancelled_participant' => [
                    'serial_number' => $participantToCancel->participant_number,
                    'name' => $participantToCancel->firstname . ' ' . $participantToCancel->lastname,
                    'email' => $participantToCancel->email,
                    'whatsapp_number' => $participantToCancel->whatsapp_number,
                ],
                'remaining_participants' => $remainingParticipants->map(function ($participant) {
                    return [
                        'serial_number' => $participant->participant_number,
                        'name' => $participant->firstname . ' ' . $participant->lastname,
                        'email' => $participant->email,
                        'whatsapp_number' => $participant->whatsapp_number,
                        'role' => $participant->participant_number === 1 ? 'primary' : 'secondary',
                    ];
                })->values(),
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                ],
                'cancellation_type' => 'partial',
                'total_remaining' => $remainingParticipants->count(),
                'message' => 'Your participation cancelled successfully. Confirmation email sent to primary participant.',
            ];

            return $this->sendResponse($responseData, 'Your participation cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Database error during secondary cancellation: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel your participation due to database error');
        }
    }

    private function processCompleteCancellation($participantToCancel, $retreat, $allParticipants): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Mark all participants as inactive
            Booking::where('booking_id', $participantToCancel->booking_id)
                ->update([
                    'is_active' => false,
                    'updated_by' => null,
                ]);

            // Get primary booking for email
            $primaryBooking = $allParticipants->where('participant_number', 1)->first();
            if (!$primaryBooking) {
                $primaryBooking = $participantToCancel; // Fallback if primary not found
            }

            // Send complete cancellation email
            try {
                Mail::to($primaryBooking->email)
                    ->send(new BookingCancellation(
                        $primaryBooking,
                        $retreat,
                        $participantToCancel,
                        collect(), // No remaining participants
                        'full'
                    ));
            } catch (\Exception $e) {
                \Log::error('Failed to send complete cancellation email: ' . $e->getMessage());
            }

            DB::commit();

            // Prepare response for complete cancellation
            $responseData = [
                'booking_id' => $participantToCancel->booking_id,
                'cancelled_participant' => [
                    'serial_number' => $participantToCancel->participant_number,
                    'name' => $participantToCancel->firstname . ' ' . $participantToCancel->lastname,
                    'email' => $participantToCancel->email,
                    'whatsapp_number' => $participantToCancel->whatsapp_number,
                ],
                'remaining_participants' => [],
                'retreat' => [
                    'id' => $retreat->id,
                    'name' => $retreat->title,
                    'start_date' => $retreat->start_date->format('Y-m-d'),
                    'end_date' => $retreat->end_date->format('Y-m-d'),
                ],
                'cancellation_type' => 'complete',
                'total_remaining' => 0,
                'message' => 'Complete booking cancelled successfully. All participants have been cancelled.',
            ];

            return $this->sendResponse($responseData, 'Complete booking cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Database error during complete cancellation: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel complete booking due to database error');
        }
    }
}
