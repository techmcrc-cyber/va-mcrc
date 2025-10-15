<?php

namespace App\Imports;

use App\Models\Booking;
use App\Models\Retreat;
use App\Http\Requests\BookingRequest;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingsImport implements ToCollection, WithHeadingRow
{
    protected $retreatId;
    protected $isPreview;
    protected $previewData = [];
    protected $importResults = ['success' => 0, 'errors' => 0];

    public function __construct($retreatId, $isPreview = false)
    {
        $this->retreatId = $retreatId;
        $this->isPreview = $isPreview;
    }

    public function collection(Collection $rows)
    {
        if ($this->isPreview) {
            $this->processPreview($rows);
        }
    }

    protected function processPreview(Collection $rows)
    {
        $retreat = Retreat::find($this->retreatId);
        
        foreach ($rows as $index => $row) {
            $rowData = $this->mapRowData($row);
            $validation = $this->validateRow($rowData, $retreat);
            
            $this->previewData[] = [
                'row_number' => $index + 2, // +2 because of header and 0-based index
                'data' => $rowData,
                'validation' => $validation,
                'status' => $validation['is_valid'] ? 'success' : 'error',
                'errors' => $validation['errors'] ?? []
            ];
        }
    }

    protected function mapRowData($row)
    {
        return [
            'group_id' => (int)($row['group_id'] ?? 1),
            'firstname' => trim($row['first_name'] ?? ''),
            'lastname' => trim($row['last_name'] ?? ''),
            'email' => trim($row['email'] ?? ''),
            'whatsapp_number' => $this->sanitizePhoneNumber($row['whatsapp_number'] ?? ''),
            'age' => (int)($row['age'] ?? 0),
            'gender' => strtolower(trim($row['gender'] ?? '')),
            'married' => !empty($row['married']) ? strtolower(trim($row['married'])) : null,
            'address' => trim($row['address'] ?? ''),
            'city' => trim($row['city'] ?? ''),
            'state' => trim($row['state'] ?? ''),
            'diocese' => !empty($row['diocese']) ? trim($row['diocese']) : null,
            'parish' => !empty($row['parish']) ? trim($row['parish']) : null,
            'congregation' => !empty($row['congregation']) ? trim($row['congregation']) : null,
            'emergency_contact_name' => trim($row['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' => $this->sanitizePhoneNumber($row['emergency_contact_phone'] ?? ''),
            'special_remarks' => !empty($row['special_remarks']) ? trim($row['special_remarks']) : null,
        ];
    }

    protected function validateRow($data, $retreat)
    {
        // Use the same validation rules as BookingRequest, but flexible for additional participants
        $rules = [
            'group_id' => ['required', 'integer', 'min:1'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'gender' => ['required', 'in:male,female,other'],
            'married' => ['nullable', 'in:yes,no'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'diocese' => ['nullable', 'string', 'max:255'],
            'parish' => ['nullable', 'string', 'max:255'],
            'congregation' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'min:10', 'max:15'],
            'special_remarks' => ['nullable', 'string'],
        ];
        
        // Ensure phone fields are strings after sanitization
        $data['whatsapp_number'] = (string)$data['whatsapp_number'];
        $data['emergency_contact_phone'] = (string)$data['emergency_contact_phone'];
        
        // Email and WhatsApp are more flexible - not required for minors/additional participants
        if (!empty($data['email'])) {
            $rules['email'] = ['email', 'max:255'];
        }
        if (!empty($data['whatsapp_number'])) {
            $rules['whatsapp_number'] = ['string', 'size:10', 'regex:/^[0-9]{10}$/'];
        } else {
            // Allow empty whatsapp_number but ensure it's a string if provided
            $rules['whatsapp_number'] = ['nullable', 'string'];
        }

        $validator = Validator::make($data, $rules);
        
        $errors = [];
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }

        // Use CriteriaValidationService for strict validation
        $validationService = new \App\Services\CriteriaValidationService();
        $criteriaValidation = $validationService->validateWithRecurrentCheck(
            $data,
            $retreat->criteria,
            true // Strict mode - blocks both criteria failures AND recurrent bookings
        );

        // If validation fails (criteria OR recurrent booking), add to errors
        if (!$criteriaValidation['valid']) {
            $participantName = $data['firstname'] . ' ' . $data['lastname'];
            foreach ($criteriaValidation['messages'] as $message) {
                $errors[] = "{$participantName}: {$message}";
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => [], // No warnings in strict mode
            'flags' => null // No flags - strict validation blocks invalid bookings
        ];
    }



    /**
     * Ensure phone numbers are treated as strings and strip non-digits.
     * Handles scientific notation from Excel and preserves leading zeros.
     */
    protected function sanitizePhoneNumber($value)
    {
        if ($value === null || $value === '') return '';
        
        // Convert to string and trim
        $str = trim((string)$value);
        
        // Handle scientific notation from Excel (e.g., 9.12345E+9)
        if (preg_match('/^\d+\.?\d*[eE][+-]?\d+$/', $str)) {
            // Convert scientific notation to regular number
            $number = (float)$str;
            $str = sprintf('%.0f', $number);
        }
        
        // Remove all non-digits to get clean phone number
        $digits = preg_replace('/\D+/', '', $str);
        
        // Return the digits as string (preserves leading zeros)
        return (string)$digits;
    }

    public function getPreviewData()
    {
        return $this->previewData;
    }

    public function setPreviewData($data)
    {
        $this->previewData = $data;
    }

    public function processImport()
    {
        // Group participants by group_id
        $groupedData = collect($this->previewData)
            ->where('validation.is_valid', true)
            ->groupBy('data.group_id');
        
        $maxAdditionalMembers = config('bookings.max_additional_members', 3);
        
        foreach ($groupedData as $groupId => $participants) {
            try {
                // Validate group size
                if (count($participants) > ($maxAdditionalMembers + 1)) {
                    throw new \Exception("Group {$groupId} has too many participants. Maximum allowed: " . ($maxAdditionalMembers + 1));
                }
                
                $this->createGroupBooking($participants, $groupId);
            } catch (\Exception $e) {
                // If group fails, mark all participants in group as errors
                $this->importResults['errors'] += count($participants);
            }
        }
        
        // Count errors for invalid participants
        $invalidCount = collect($this->previewData)
            ->where('validation.is_valid', false)
            ->count();
        $this->importResults['errors'] += $invalidCount;
    }

    protected function createGroupBooking($participants, $groupId)
    {
        $bookingId = Booking::generateBookingId();
        $userId = Auth::id();
        
        // Sort participants - ensure primary participant is first
        $sortedParticipants = $participants->sortBy(function ($participant, $index) {
            // First row in group becomes primary participant
            return $index;
        });
        
        $participantNumber = 1;
        $additionalCount = count($participants) - 1;
        $primaryBooking = null;
        $allBookings = [];
        
        foreach ($sortedParticipants as $participant) {
            $data = $participant['data'];
            
            $booking = Booking::create([
                'booking_id' => $bookingId,
                'retreat_id' => $this->retreatId,
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'whatsapp_number' => $data['whatsapp_number'] ?: null,
                'age' => $data['age'],
                'email' => $data['email'] ?: null,
                'address' => $data['address'],
                'gender' => $data['gender'],
                'married' => $data['married'],
                'city' => $data['city'],
                'state' => $data['state'],
                'diocese' => $data['diocese'],
                'parish' => $data['parish'],
                'congregation' => $data['congregation'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'additional_participants' => $participantNumber === 1 ? $additionalCount : 0,
                'special_remarks' => $data['special_remarks'],
                'flag' => null, // No flags - strict validation blocks invalid bookings
                'participant_number' => $participantNumber,
                'created_by' => $userId,
                'updated_by' => $userId,
                'is_active' => true,
            ]);
            
            $allBookings[] = $booking;
            
            if ($participantNumber === 1) {
                $primaryBooking = $booking;
            }
            
            $this->importResults['success']++;
            $participantNumber++;
        }
        
        // Queue confirmation email to primary booking contact
        if ($primaryBooking && $primaryBooking->email) {
            $retreat = Retreat::find($this->retreatId);
            \App\Jobs\SendBookingConfirmationEmail::dispatch($primaryBooking, $retreat, collect($allBookings));
        }
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
}