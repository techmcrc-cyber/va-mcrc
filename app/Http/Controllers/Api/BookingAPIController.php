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
    /**
     * Store a newly created booking.
     * Accept JSON array of participant objects.
     */
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

            // Get retreat details
            $retreat = Retreat::with(['bookings' => function($query) {
                $query->active();
            }])->where('id', $request->retreat_id)
              ->active()
              ->first();

            if (!$retreat) {
                return $this->sendNotFound('Retreat not found or inactive');
            }

            // Check if retreat is available for booking
            if ($retreat->start_date->isPast()) {
                return $this->sendError('This retreat has already started or ended', 'RETREAT_PAST');
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

            // Validate each participant
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

            // Check for duplicate participants and business rules
            $businessRuleErrors = $this->validateBusinessRules($participants, $retreat);
            if (!empty($businessRuleErrors)) {
                return $this->sendError('Business rule validation failed', 'BUSINESS_RULES_FAILED', 400, $businessRuleErrors);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Generate unique booking ID
                $bookingId = Booking::generateBookingId();
                $allBookings = [];
                $primaryBooking = null;

                // Create bookings for all participants
                foreach ($participants as $index => $participantData) {
                    $serialNumber = $index + 1;
                    
                    // Create booking record
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
                        'created_by' => null, // API bookings don't have user context
                        'updated_by' => null,
                    ]);

                    $allBookings[] = $booking;
                    
                    if ($serialNumber === 1) {
                        $primaryBooking = $booking;
                    }
                }

                // Send confirmation email to primary participant
                if ($primaryBooking) {
                    try {
                        Mail::to($primaryBooking->email)
                            ->send(new BookingConfirmation($primaryBooking, $retreat, $allBookings));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
                        // Don't fail the booking if email fails
                    }
                }

                DB::commit();

                // Prepare response
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

    /**
     * Display the specified booking.
     * Requires booking_id and whatsapp_number for validation.
     */
    public function show(Request $request): JsonResponse
    {
        try {
            // Validate required parameters (can be in headers or query params)
            $bookingId = $request->header('booking-id') ?? $request->query('booking_id');
            $whatsappNumber = $request->header('whatsapp-number') ?? $request->query('whatsapp_number');

            $validator = Validator::make([
                'booking_id' => $bookingId,
                'whatsapp_number' => $whatsappNumber,
            ], [
                'booking_id' => 'required|string',
                'whatsapp_number' => 'required|numeric|digits:10',
            ], [
                'booking_id.required' => 'Booking ID is required in headers or query parameters',
                'whatsapp_number.required' => 'WhatsApp number is required in headers or query parameters',
                'whatsapp_number.digits' => 'WhatsApp number must be exactly 10 digits',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            // Find the primary booking (participant_number = 1) with the given booking_id and whatsapp_number
            $primaryBooking = Booking::with(['retreat'])
                ->where('booking_id', $bookingId)
                ->where('whatsapp_number', $whatsappNumber)
                ->where('participant_number', 1) // Primary participant only
                ->where('is_active', true)
                ->first();

            if (!$primaryBooking) {
                return $this->sendError(
                    'Unable to retrieve booking information for the specified Booking ID and Whatsapp number',
                    'BOOKING_NOT_FOUND',
                    400
                );
            }

            // Get all participants for this booking
            $allParticipants = Booking::where('booking_id', $bookingId)
                ->where('is_active', true)
                ->orderBy('participant_number')
                ->get();

            $retreat = $primaryBooking->retreat;

            // Check if retreat still exists and is active
            if (!$retreat || !$retreat->is_active) {
                return $this->sendError('Associated retreat is no longer available', 'RETREAT_UNAVAILABLE');
            }

            // Calculate booking status
            $now = now();
            $retreatStatus = 'upcoming';
            if ($retreat->end_date->isPast()) {
                $retreatStatus = 'completed';
            } elseif ($retreat->start_date->isPast() && $retreat->end_date->isFuture()) {
                $retreatStatus = 'ongoing';
            }

            // Prepare comprehensive response
            $bookingDetails = [
                'booking_id' => $primaryBooking->booking_id,
                'booking_date' => $primaryBooking->created_at->format('Y-m-d H:i:s'),
                'status' => 'confirmed', // Since we only show active bookings
                'retreat_status' => $retreatStatus,
                
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
                    // 'location' => [
                    //     'name' => $retreat->location,
                    //     'address' => $retreat->address,
                    //     'city' => $retreat->city,
                    //     'state' => $retreat->state,
                    //     'country' => $retreat->country,
                    //     'postal_code' => $retreat->postal_code,
                    //     'coordinates' => [
                    //         'latitude' => $retreat->latitude,
                    //         'longitude' => $retreat->longitude,
                    //     ],
                    // ],
                    // 'pricing' => [
                    //     'price' => (float) $retreat->price,
                    //     'discount_price' => $retreat->discount_price ? (float) $retreat->discount_price : null,
                    //     'effective_price' => (float) ($retreat->discount_price ?? $retreat->price),
                    // ],
                    'criteria' => [
                        'type' => $retreat->criteria,
                        'label' => $retreat->criteria_label,
                    ],
                    'instructions' => $retreat->instructions,
                    'special_remarks' => $retreat->special_remarks,
                    // 'category' => $retreat->category ? [
                    //     'id' => $retreat->category->id,
                    //     'name' => $retreat->category->name,
                    // ] : null,
                    // 'featured_image' => $retreat->featured_image_url,
                ],
                
                // Primary Participant Details
                'primary_participant' => [
                    'serial_number' => $primaryBooking->participant_number,
                    'firstname' => $primaryBooking->firstname,
                    'lastname' => $primaryBooking->lastname,
                    'full_name' => $primaryBooking->firstname . ' ' . $primaryBooking->lastname,
                    'email' => $primaryBooking->email,
                    'whatsapp_number' => $primaryBooking->whatsapp_number,
                    'age' => $primaryBooking->age,
                    'gender' => ucfirst($primaryBooking->gender),
                    'address' => $primaryBooking->address,
                    'city' => $primaryBooking->city,
                    'state' => $primaryBooking->state,
                    'diocese' => $primaryBooking->diocese,
                    'parish' => $primaryBooking->parish,
                    'congregation' => $primaryBooking->congregation,
                    'emergency_contact' => [
                        'name' => $primaryBooking->emergency_contact_name,
                        'phone' => $primaryBooking->emergency_contact_phone,
                    ],
                ],
                
                // All Participants
                'participants' => $allParticipants->map(function ($participant) {
                    return [
                        'serial_number' => $participant->participant_number,
                        'firstname' => $participant->firstname,
                        'lastname' => $participant->lastname,
                        'full_name' => $participant->firstname . ' ' . $participant->lastname,
                        'email' => $participant->email,
                        'whatsapp_number' => $participant->whatsapp_number,
                        'age' => $participant->age,
                        'gender' => ucfirst($participant->gender),
                        'role' => $participant->participant_number === 1 ? 'primary' : 'secondary',
                        'emergency_contact' => [
                            'name' => $participant->emergency_contact_name,
                            'phone' => $participant->emergency_contact_phone,
                        ],
                    ];
                })->values(),
                
                // Booking Summary
                'summary' => [
                    'total_participants' => $allParticipants->count(),
                    'additional_participants' => $primaryBooking->additional_participants,
                    'special_remarks' => $primaryBooking->special_remarks,
                    'booking_flags' => $primaryBooking->flag ? explode(',', $primaryBooking->flag) : [],
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

            return $this->sendResponse($bookingDetails, 'Booking details retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('API - Failed to retrieve booking details: ' . $e->getMessage());
            return $this->sendServerError('Failed to retrieve booking details');
        }
    }

    /**
     * Partially cancel booking.
     * Cancel specific participant by their serial number.
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            // Validate input parameters
            $validator = Validator::make(array_merge($request->all(), ['booking_id' => $id]), [
                'booking_id' => 'required|string',
                'serial_number' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator->errors());
            }

            $bookingId = $id;
            $serialNumber = $request->input('serial_number');

            // Find the specific participant to cancel
            $participantToCancel = Booking::where('booking_id', $bookingId)
                ->where('participant_number', $serialNumber)
                ->where('is_active', true)
                ->first();

            if (!$participantToCancel) {
                return $this->sendError(
                    'Unable to cancel booking for the specified Booking ID and participant serial number',
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

            // Check if this is the last participant (would result in complete cancellation)
            if ($allParticipants->count() === 1) {
                // This is a complete cancellation
                return $this->processCompleteCancellation($participantToCancel, $retreat, $allParticipants);
            }

            // Process partial cancellation
            DB::beginTransaction();

            try {
                // Mark participant as inactive (soft cancel)
                $participantToCancel->update([
                    'is_active' => false,
                    'updated_by' => null, // API cancellations don't have user context
                ]);

                // Get remaining active participants
                $remainingParticipants = $allParticipants->where('id', '!=', $participantToCancel->id);

                // Get primary booking for email (participant_number = 1)
                $primaryBooking = $remainingParticipants->where('participant_number', 1)->first() ?:
                                 $allParticipants->where('participant_number', 1)->first();

                if (!$primaryBooking) {
                    throw new \Exception('Primary booking not found');
                }

                // Update additional_participants count for primary booking
                if ($primaryBooking && $primaryBooking->is_active) {
                    $activeCount = $remainingParticipants->count();
                    $primaryBooking->update([
                        'additional_participants' => max(0, $activeCount - 1), // Subtract 1 for primary
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
                    // Don't fail the cancellation if email fails
                }

                DB::commit();

                // Prepare response
                $responseData = [
                    'booking_id' => $bookingId,
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
                    'message' => 'Participant cancelled successfully. Confirmation email sent to primary participant.',
                ];

                return $this->sendResponse($responseData, 'Booking participant cancelled successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error during partial cancellation: ' . $e->getMessage());
                return $this->sendServerError('Failed to cancel participant due to database error');
            }

        } catch (\Exception $e) {
            \Log::error('API - Failed to cancel booking participant: ' . $e->getMessage());
            return $this->sendServerError('Failed to cancel booking participant');
        }
    }

    /**
     * Get validation rules for each participant based on retreat criteria.
     */
    private function getParticipantValidationRules(Retreat $retreat): array
    {
        // Base validation rules for all participants
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'whatsapp_number' => 'required|numeric|digits:10',
            'age' => 'required|integer|min:1|max:120',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'gender' => 'required|in:male,female,other',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'diocese' => 'nullable|string|max:255',
            'parish' => 'nullable|string|max:255',
            'congregation' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'special_remarks' => 'nullable|string|max:1000',
        ];

        // Add specific rules based on retreat criteria
        if (in_array($retreat->criteria, ['priests_only', 'sisters_only'])) {
            $rules['congregation'] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Validate business rules for participants.
     */
    private function validateBusinessRules(array $participants, Retreat $retreat): array
    {
        $errors = [];

        // Validate each participant against retreat criteria and recurrent booking rules
        foreach ($participants as $index => $participant) {
            $participantPosition = $index + 1;

            // Check retreat criteria compliance
            if (!$this->meetsRetreatCriteria($participant, $retreat)) {
                $criteriaLabel = $retreat->criteria_label ?? $retreat->criteria;
                $errors[] = "Participant {$participantPosition} ({$participant['firstname']} {$participant['lastname']}) does not meet retreat criteria: {$criteriaLabel}";
            }

            // Check for recurrent bookings (within past year) and add RECURRENT_BOOKING remark
            $hasRecentBooking = Booking::hasAttendedInPastYear(
                $participant['whatsapp_number'],
                $participant['firstname'],
                $participant['lastname']
            );

            if ($hasRecentBooking) {
                $errors[] = "Participant {$participantPosition} ({$participant['firstname']} {$participant['lastname']}) has attended a retreat in the past year - RECURRENT_BOOKING";
            }
        }

        return $errors;
    }

    /**
     * Check if participant meets retreat criteria.
     */
    private function meetsRetreatCriteria(array $participant, Retreat $retreat): bool
    {
        if ($retreat->criteria === 'no_criteria') {
            return true;
        }

        $criteriaCheck = [
            'male_only' => $participant['gender'] === 'male',
            'female_only' => $participant['gender'] === 'female',
            'priests_only' => !empty($participant['congregation']),
            'sisters_only' => $participant['gender'] === 'female' && !empty($participant['congregation']),
            'youth_only' => $participant['age'] >= 16 && $participant['age'] <= 30,
            'children' => $participant['age'] <= 15,
        ];

        return $criteriaCheck[$retreat->criteria] ?? false;
    }

    /**
     * Process complete cancellation when last participant is cancelled.
     */
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
                // Don't fail the cancellation if email fails
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
