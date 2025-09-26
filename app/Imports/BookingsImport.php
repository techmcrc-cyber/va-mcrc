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
        // Use the same validation rules as BookingRequest
        $rules = [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'gender' => ['required', 'in:male,female,other'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'diocese' => ['nullable', 'string', 'max:255'],
            'parish' => ['nullable', 'string', 'max:255'],
            'congregation' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_phone' => ['required', 'string', 'min:10', 'max:15'],
            'special_remarks' => ['nullable', 'string'],
        ];

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
        foreach ($this->previewData as $item) {
            if ($item['validation']['is_valid']) {
                try {
                    $this->createBooking($item['data'], $item['validation']['flags']);
                    $this->importResults['success']++;
                } catch (\Exception $e) {
                    $this->importResults['errors']++;
                }
            } else {
                $this->importResults['errors']++;
            }
        }
    }

    protected function createBooking($data, $flags)
    {
        $bookingId = Booking::generateBookingId();
        $userId = Auth::id();

        Booking::create([
            'booking_id' => $bookingId,
            'retreat_id' => $this->retreatId,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'whatsapp_number' => $data['whatsapp_number'],
            'age' => $data['age'],
            'email' => $data['email'],
            'address' => $data['address'],
            'gender' => $data['gender'],
            'city' => $data['city'],
            'state' => $data['state'],
            'diocese' => $data['diocese'],
            'parish' => $data['parish'],
            'congregation' => $data['congregation'],
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_phone' => $data['emergency_contact_phone'],
            'additional_participants' => 0,
            'special_remarks' => $data['special_remarks'],
            'flag' => $flags ?: null,
            'participant_number' => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
            'is_active' => true,
        ]);
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
}