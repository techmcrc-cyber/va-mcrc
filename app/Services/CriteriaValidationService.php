<?php

namespace App\Services;

use App\Models\Criteria;
use App\Models\Booking;

class CriteriaValidationService
{
    /**
     * Validate participant against retreat criteria
     * 
     * @param array $participantData
     * @param int|null $criteriaId
     * @param bool $strictMode - If true, returns validation result. If false, returns flags for failed fields
     * @return array
     */
    public function validateParticipant(array $participantData, ?int $criteriaId, bool $strictMode = true): array
    {
        // If no criteria set, validation passes
        if (!$criteriaId) {
            return [
                'valid' => true,
                'flags' => [],
                'messages' => []
            ];
        }

        $criteria = Criteria::find($criteriaId);
        
        if (!$criteria || !$criteria->status) {
            return [
                'valid' => true,
                'flags' => [],
                'messages' => []
            ];
        }

        $failures = [];
        $messages = [];

        // Check Gender
        if ($criteria->gender) {
            if (strtolower($participantData['gender']) !== $criteria->gender) {
                $failures[] = 'GENDER_MISMATCH';
                $messages[] = "Gender must be {$criteria->gender}";
            }
        }

        // Check Minimum Age
        if ($criteria->min_age !== null) {
            if ($participantData['age'] < $criteria->min_age) {
                $failures[] = 'MIN_AGE_FAILED';
                $messages[] = "Minimum age requirement is {$criteria->min_age}";
            }
        }

        // Check Maximum Age
        if ($criteria->max_age !== null) {
            if ($participantData['age'] > $criteria->max_age) {
                $failures[] = 'MAX_AGE_FAILED';
                $messages[] = "Maximum age limit is {$criteria->max_age}";
            }
        }

        // Check Married Status
        if ($criteria->married === 'yes') {
            // Assuming we need a 'married' field in participant data
            // If not provided, we consider it as not meeting the criteria
            $isMarried = isset($participantData['married']) && 
                         in_array(strtolower($participantData['married']), ['yes', 'true', '1', 'married']);
            
            if (!$isMarried) {
                $failures[] = 'MARRIED_STATUS_FAILED';
                $messages[] = "Only married participants are allowed";
            }
        }

        // Check Vocation
        if ($criteria->vocation) {
            $congregation = trim($participantData['congregation'] ?? '');
            
            if ($criteria->vocation === 'priest_only') {
                // Priest only - must have congregation
                if (empty($congregation)) {
                    $failures[] = 'VOCATION_PRIEST_FAILED';
                    $messages[] = "Only priests are allowed (congregation required)";
                }
            } elseif ($criteria->vocation === 'sisters_only') {
                // Sisters only - must be female with congregation
                if (strtolower($participantData['gender']) !== 'female' || empty($congregation)) {
                    $failures[] = 'VOCATION_SISTERS_FAILED';
                    $messages[] = "Only sisters are allowed (female with congregation required)";
                }
            }
        }

        $isValid = empty($failures);

        return [
            'valid' => $isValid,
            'flags' => $failures,
            'messages' => $messages,
            'criteria_name' => $criteria->name
        ];
    }

    /**
     * Check if participant has attended a retreat in the past year
     * 
     * @param string $whatsappNumber
     * @param string $firstName
     * @param string $lastName
     * @param string|null $currentBookingId
     * @return array
     */
    public function checkRecurrentBooking(
        string $whatsappNumber, 
        string $firstName, 
        string $lastName, 
        ?string $currentBookingId = null
    ): array {
        $hasAttended = Booking::hasAttendedInPastYear(
            $whatsappNumber,
            $firstName,
            $lastName,
            $currentBookingId
        );

        return [
            'is_recurrent' => $hasAttended,
            'flag' => $hasAttended ? 'RECURRENT_BOOKING' : null,
            'message' => $hasAttended ? 'Participant has already attended a retreat this year' : null
        ];
    }

    /**
     * Validate and combine all checks for a participant
     * 
     * @param array $participantData
     * @param int|null $criteriaId
     * @param bool $strictMode
     * @param string|null $currentBookingId
     * @return array
     */
    public function validateWithRecurrentCheck(
        array $participantData,
        ?int $criteriaId,
        bool $strictMode = true,
        ?string $currentBookingId = null
    ): array {
        // Validate criteria
        $criteriaValidation = $this->validateParticipant($participantData, $criteriaId, $strictMode);
        
        // Check recurrent booking
        $recurrentCheck = $this->checkRecurrentBooking(
            $participantData['whatsapp_number'] ?? '',
            $participantData['firstname'] ?? '',
            $participantData['lastname'] ?? '',
            $currentBookingId
        );

        // Combine flags
        $allFlags = $criteriaValidation['flags'];
        if ($recurrentCheck['flag']) {
            $allFlags[] = $recurrentCheck['flag'];
        }

        // Combine messages
        $allMessages = $criteriaValidation['messages'];
        if ($recurrentCheck['message']) {
            $allMessages[] = $recurrentCheck['message'];
        }

        // In strict mode, validation fails if criteria fails OR if recurrent booking detected
        $isValid = $strictMode ? ($criteriaValidation['valid'] && !$recurrentCheck['is_recurrent']) : true;

        return [
            'valid' => $isValid,
            'flags' => $allFlags,
            'flag_string' => !empty($allFlags) ? implode(',', $allFlags) : null,
            'messages' => $allMessages,
            'criteria_validation' => $criteriaValidation,
            'recurrent_check' => $recurrentCheck
        ];
    }

    /**
     * Format validation errors for API response
     * 
     * @param array $validation
     * @return array
     */
    public function formatValidationErrors(array $validation): array
    {
        return [
            'valid' => $validation['valid'],
            'errors' => $validation['messages'],
            'flags' => $validation['flags']
        ];
    }
}
