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
            'firstname' => $row['first_name'] ?? '',
            'lastname' => $row['last_name'] ?? '',
            'email' => $row['email'] ?? '',
            'whatsapp_number' => $row['whatsapp_number'] ?? '',
            'age' => (int)($row['age'] ?? 0),
            'gender' => strtolower($row['gender'] ?? ''),
            'address' => $row['address'] ?? '',
            'city' => $row['city'] ?? '',
            'state' => $row['state'] ?? '',
            'diocese' => $row['diocese'] ?? null,
            'parish' => $row['parish'] ?? null,
            'congregation' => $row['congregation'] ?? null,
            'emergency_contact_name' => $row['emergency_contact_name'] ?? '',
            'emergency_contact_phone' => $row['emergency_contact_phone'] ?? '',
            'special_remarks' => $row['special_remarks'] ?? null,
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
        
        // Email and WhatsApp are more flexible - not required for minors/additional participants
        if (!empty($data['email'])) {
            $rules['email'] = ['email', 'max:255'];
        }
        if (!empty($data['whatsapp_number'])) {
            $rules['whatsapp_number'] = ['string', 'size:10', 'regex:/^[0-9]{10}$/'];
        }

        // Add retreat-specific validation
        if ($retreat->criteria === 'priests_only' || $retreat->criteria === 'sisters_only') {
            $rules['congregation'] = ['required', 'string', 'max:255'];
        }

        $validator = Validator::make($data, $rules);
        
        $errors = [];
        $warnings = [];
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
        }

        // Check retreat criteria
        $criteriaCheck = $this->checkRetreatCriteria($data, $retreat);
        if (!$criteriaCheck['meets_criteria']) {
            $warnings[] = $criteriaCheck['message'];
        }

        // Check for duplicate bookings
        $duplicateCheck = $this->checkForDuplicates($data);
        if ($duplicateCheck['is_duplicate']) {
            $warnings[] = $duplicateCheck['message'];
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'flags' => $this->generateFlags($data, $retreat, $duplicateCheck, $criteriaCheck)
        ];
    }

    protected function checkRetreatCriteria($data, $retreat)
    {
        if ($retreat->criteria === 'no_criteria') {
            return ['meets_criteria' => true];
        }

        $criteriaCheck = [
            'male_only' => $data['gender'] === 'male',
            'female_only' => $data['gender'] === 'female',
            'priests_only' => !empty(trim($data['congregation'] ?? '')),
            'sisters_only' => $data['gender'] === 'female' && !empty(trim($data['congregation'] ?? '')),
            'youth_only' => $data['age'] >= 16 && $data['age'] <= 30,
            'children' => $data['age'] <= 15,
        ];

        $meetsCriteria = $criteriaCheck[$retreat->criteria] ?? false;

        $messages = [
            'male_only' => 'Only male participants are allowed for this retreat',
            'female_only' => 'Only female participants are allowed for this retreat',
            'priests_only' => 'Only priests are allowed for this retreat (congregation required)',
            'sisters_only' => 'Only sisters are allowed for this retreat (female + congregation required)',
            'youth_only' => 'Only youth (age 16-30) are allowed for this retreat',
            'children' => 'Only children (age 15 or below) are allowed for this retreat',
        ];

        return [
            'meets_criteria' => $meetsCriteria,
            'message' => $meetsCriteria ? '' : ($messages[$retreat->criteria] ?? 'Does not meet retreat criteria')
        ];
    }

    protected function checkForDuplicates($data)
    {
        $exists = Booking::where('whatsapp_number', $data['whatsapp_number'])
            ->where('firstname', $data['firstname'])
            ->where('lastname', $data['lastname'])
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subYear())
            ->exists();

        return [
            'is_duplicate' => $exists,
            'message' => $exists ? 'Participant has already booked a retreat in the past year' : ''
        ];
    }

    protected function generateFlags($data, $retreat, $duplicateCheck, $criteriaCheck)
    {
        $flags = [];

        if ($duplicateCheck['is_duplicate']) {
            $flags[] = 'RECURRENT_BOOKING';
        }

        if (!$criteriaCheck['meets_criteria']) {
            $flags[] = 'CRITERIA_FAILED';
        }

        return implode(',', $flags);
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
        
        foreach ($sortedParticipants as $participant) {
            $data = $participant['data'];
            $flags = $participant['validation']['flags'] ?? '';
            
            Booking::create([
                'booking_id' => $bookingId,
                'retreat_id' => $this->retreatId,
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'whatsapp_number' => $data['whatsapp_number'] ?: null,
                'age' => $data['age'],
                'email' => $data['email'] ?: null,
                'address' => $data['address'],
                'gender' => $data['gender'],
                'city' => $data['city'],
                'state' => $data['state'],
                'diocese' => $data['diocese'],
                'parish' => $data['parish'],
                'congregation' => $data['congregation'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'additional_participants' => $participantNumber === 1 ? $additionalCount : 0,
                'special_remarks' => $data['special_remarks'],
                'flag' => $flags ?: null,
                'participant_number' => $participantNumber,
                'created_by' => $userId,
                'updated_by' => $userId,
                'is_active' => true,
            ]);
            
            $this->importResults['success']++;
            $participantNumber++;
        }
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
}