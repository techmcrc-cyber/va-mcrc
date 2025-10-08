<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Retreat;
use App\Services\CriteriaValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecialBookingController extends Controller
{
    protected $validationService;

    public function __construct()
    {
        $this->validationService = new CriteriaValidationService();
    }

    /**
     * Display a listing of special bookings
     */
    public function index(Request $request)
    {
        // Similar to regular bookings index but filtered for special bookings
        if ($request->ajax()) {
            $query = Booking::with(['retreat'])
                ->whereNotNull('flag')
                ->where('is_active', true);
            
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('booking_id', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('whatsapp_number', 'like', "%{$search}%");
                });
            }
            
            // Order by booking_id in descending order
            $query->orderBy('booking_id', 'desc');
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $bookings = $query->offset($start)->limit($limit)->get();
            
            $data = [];
            foreach ($bookings as $booking) {
                $nestedData = [];
                $nestedData['booking_id'] = $booking->booking_id;
                $nestedData['name'] = $booking->firstname . ' ' . $booking->lastname;
                $nestedData['retreat'] = $booking->retreat ? $booking->retreat->title : '-';
                $nestedData['flags'] = $booking->flag ? '<span class="badge bg-warning">' . str_replace(',', '</span> <span class="badge bg-warning">', $booking->flag) . '</span>' : '-';
                $nestedData['created_at'] = $booking->created_at->format('M d, Y');
                
                // Check if retreat has ended
                $hasEnded = $booking->retreat && $booking->retreat->end_date < now();
                
                // Actions - matching normal bookings design
                $actions = '<div class="btn-row mb-1">';
                $actions .= '<a href="' . route('admin.special-bookings.show', $booking) . '" class="btn btn-sm btn-info me-1" title="View">';
                $actions .= '<i class="fas fa-eye"></i></a> ';
                
                if ($hasEnded) {
                    // Disabled Edit button for past retreats
                    $actions .= '<button class="btn btn-sm btn-primary" title="Cannot edit past retreat" disabled>';
                    $actions .= '<i class="fas fa-edit"></i></button>';
                } else {
                    // Active Edit button for current/future retreats
                    $actions .= '<a href="' . route('admin.special-bookings.edit', $booking) . '" class="btn btn-sm btn-primary" title="Edit">';
                    $actions .= '<i class="fas fa-edit"></i></a>';
                }
                $actions .= '</div>';
                
                $actions .= '<div class="btn-row">';
                if ($hasEnded) {
                    // Disabled Cancel button for past retreats
                    $actions .= '<button class="btn btn-sm btn-danger w-100" title="Cannot cancel past retreat" disabled>';
                    $actions .= '<i class="fas fa-ban"></i></button>';
                } else {
                    // Active Cancel button for current/future retreats
                    $actions .= '<form action="' . route('admin.bookings.cancel-participant', $booking) . '" method="POST" class="d-inline w-100">';
                    $actions .= csrf_field();
                    $actions .= method_field('DELETE');
                    $actions .= '<button type="submit" class="btn btn-sm btn-danger w-100" title="Cancel Booking" ';
                    $actions .= 'onclick="return confirm(\'Are you sure you want to cancel this special booking? This will deactivate all participants in this booking.\')">';
                    $actions .= '<i class="fas fa-ban"></i></button></form>';
                }
                $actions .= '</div>';
                
                $nestedData['actions'] = $actions;
                
                $data[] = $nestedData;
            }
            
            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data" => $data
            ]);
        }
        
        return view('admin.special-bookings.index');
    }

    /**
     * Show the form for creating a new special booking
     */
    public function create()
    {
        $retreats = Retreat::active()->upcoming()->orderBy('start_date')->get();
        return view('admin.special-bookings.create', compact('retreats'));
    }

    /**
     * Store a newly created special booking (bypasses criteria validation)
     */
    public function store(BookingRequest $request)
    {
        $bookingData = $request->validated();
        $retreat = Retreat::findOrFail($bookingData['retreat_id']);
        
        // Validate with NON-STRICT mode (allows booking but flags failures)
        $primaryValidation = $this->validationService->validateWithRecurrentCheck(
            $bookingData,
            $retreat->criteria,
            false // Non-strict mode - allows booking with flags
        );
        
        $bookingId = Booking::generateBookingId();
        $userId = Auth::id();
        
        // Create primary booking with detailed flags
        $primaryBooking = Booking::create([
            'booking_id' => $bookingId,
            'retreat_id' => $bookingData['retreat_id'],
            'firstname' => $bookingData['firstname'],
            'lastname' => $bookingData['lastname'],
            'whatsapp_number' => $bookingData['whatsapp_number'],
            'age' => $bookingData['age'],
            'email' => $bookingData['email'],
            'address' => $bookingData['address'],
            'gender' => $bookingData['gender'],
            'married' => $bookingData['married'] ?? null,
            'city' => $bookingData['city'],
            'state' => $bookingData['state'],
            'diocese' => $bookingData['diocese'] ?? null,
            'parish' => $bookingData['parish'] ?? null,
            'congregation' => $bookingData['congregation'] ?? null,
            'emergency_contact_name' => $bookingData['emergency_contact_name'],
            'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
            'additional_participants' => $bookingData['additional_participants'],
            'special_remarks' => $bookingData['special_remarks'] ?? null,
            'flag' => $primaryValidation['flag_string'], // Detailed flags
            'participant_number' => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
            'is_active' => true,
        ]);
        
        // Create additional participants
        $participants = $bookingData['participants'] ?? [];
        $participantNumber = 2;
        
        foreach ($participants as $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue;
            }
            
            $participantValidation = $this->validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                false // Non-strict mode
            );
            
            Booking::create([
                'booking_id' => $bookingId,
                'retreat_id' => $bookingData['retreat_id'],
                'firstname' => $participant['firstname'] ?? '',
                'lastname' => $participant['lastname'] ?? '',
                'whatsapp_number' => $participant['whatsapp_number'] ?? '',
                'age' => $participant['age'] ?? null,
                'email' => $participant['email'] ?? null,
                'gender' => $participant['gender'] ?? 'other',
                'participant_number' => $participantNumber,
                'flag' => $participantValidation['flag_string'], // Detailed flags
                'created_by' => $userId,
                'updated_by' => $userId,
                'address' => $participant['address'] ?? '',
                'city' => $participant['city'] ?? '',
                'state' => $participant['state'] ?? '',
                'emergency_contact_name' => $bookingData['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $bookingData['emergency_contact_phone'] ?? '',
                'is_active' => true,
            ]);
            
            $participantNumber++;
        }
        
        $warningMessage = 'Special booking created successfully.';
        if ($primaryValidation['flag_string']) {
            $warningMessage .= ' Warning: ' . implode(', ', $primaryValidation['messages']);
        }
        
        return redirect()
            ->route('admin.special-bookings.show', $primaryBooking->id)
            ->with('warning', $warningMessage);
    }

    /**
     * Display the specified special booking
     */
    public function show(Booking $specialBooking)
    {
        $specialBooking->load('retreat');
        $allParticipants = Booking::where('booking_id', $specialBooking->booking_id)
            ->where('is_active', true)
            ->orderBy('participant_number')
            ->get();
        
        return view('admin.special-bookings.show', compact('specialBooking', 'allParticipants'));
    }

    /**
     * Show the form for editing the specified special booking
     */
    public function edit(Booking $specialBooking)
    {
        $retreats = Retreat::active()->upcoming()->orderBy('start_date')->get();
        $allParticipants = Booking::where('booking_id', $specialBooking->booking_id)
            ->where('is_active', true)
            ->orderBy('participant_number')
            ->get();
        
        return view('admin.special-bookings.edit', compact('specialBooking', 'retreats', 'allParticipants'));
    }

    /**
     * Update the specified special booking (bypasses criteria validation)
     */
    public function update(BookingRequest $request, Booking $specialBooking)
    {
        $bookingData = $request->validated();
        $retreat = Retreat::findOrFail($bookingData['retreat_id']);
        
        // Get all participants for this booking
        $allParticipants = Booking::where('booking_id', $specialBooking->booking_id)
            ->where('is_active', true)
            ->orderBy('participant_number')
            ->get();
        
        $primaryBooking = $allParticipants->where('participant_number', 1)->first();
        
        // Validate primary participant with NON-STRICT mode
        $primaryValidation = $this->validationService->validateWithRecurrentCheck(
            $bookingData,
            $retreat->criteria,
            false, // Non-strict mode
            $specialBooking->booking_id
        );
        
        // Update primary booking
        $primaryBooking->update([
            'retreat_id' => $bookingData['retreat_id'],
            'firstname' => $bookingData['firstname'],
            'lastname' => $bookingData['lastname'],
            'whatsapp_number' => $bookingData['whatsapp_number'],
            'age' => $bookingData['age'],
            'email' => $bookingData['email'],
            'address' => $bookingData['address'],
            'gender' => $bookingData['gender'],
            'married' => $bookingData['married'] ?? null,
            'city' => $bookingData['city'],
            'state' => $bookingData['state'],
            'diocese' => $bookingData['diocese'] ?? null,
            'parish' => $bookingData['parish'] ?? null,
            'congregation' => $bookingData['congregation'] ?? null,
            'emergency_contact_name' => $bookingData['emergency_contact_name'],
            'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
            'additional_participants' => $bookingData['additional_participants'],
            'special_remarks' => $bookingData['special_remarks'] ?? null,
            'flag' => $primaryValidation['flag_string'],
            'updated_by' => Auth::id(),
        ]);
        
        // Handle deleted participants
        $submittedParticipantIds = collect($bookingData['participants'] ?? [])->pluck('id')->filter();
        $existingParticipantIds = $allParticipants->where('participant_number', '>', 1)->pluck('id');
        $deletedParticipantIds = $existingParticipantIds->diff($submittedParticipantIds);
        
        foreach ($deletedParticipantIds as $deletedId) {
            Booking::find($deletedId)->update(['is_active' => false]);
        }
        
        // Update or create additional participants
        $participants = $bookingData['participants'] ?? [];
        $participantNumber = 2;
        
        foreach ($participants as $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue;
            }
            
            $participantValidation = $this->validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                false, // Non-strict mode
                $specialBooking->booking_id
            );
            
            if (isset($participant['id']) && $participant['id']) {
                // Update existing participant
                $existingParticipant = Booking::find($participant['id']);
                if ($existingParticipant) {
                    $existingParticipant->update([
                        'retreat_id' => $bookingData['retreat_id'],
                        'firstname' => $participant['firstname'] ?? '',
                        'lastname' => $participant['lastname'] ?? '',
                        'whatsapp_number' => $participant['whatsapp_number'] ?? '',
                        'age' => $participant['age'] ?? null,
                        'email' => $participant['email'] ?? null,
                        'gender' => $participant['gender'] ?? 'other',
                        'flag' => $participantValidation['flag_string'],
                        'updated_by' => Auth::id(),
                    ]);
                }
            } else {
                // Create new participant
                Booking::create([
                    'booking_id' => $specialBooking->booking_id,
                    'retreat_id' => $bookingData['retreat_id'],
                    'firstname' => $participant['firstname'] ?? '',
                    'lastname' => $participant['lastname'] ?? '',
                    'whatsapp_number' => $participant['whatsapp_number'] ?? '',
                    'age' => $participant['age'] ?? null,
                    'email' => $participant['email'] ?? null,
                    'gender' => $participant['gender'] ?? 'other',
                    'participant_number' => $participantNumber,
                    'flag' => $participantValidation['flag_string'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'address' => $participant['address'] ?? '',
                    'city' => $participant['city'] ?? '',
                    'state' => $participant['state'] ?? '',
                    'emergency_contact_name' => $bookingData['emergency_contact_name'] ?? '',
                    'emergency_contact_phone' => $bookingData['emergency_contact_phone'] ?? '',
                    'is_active' => true,
                ]);
            }
            
            $participantNumber++;
        }
        
        $warningMessage = 'Special booking updated successfully.';
        if ($primaryValidation['flag_string']) {
            $warningMessage .= ' Warning: ' . implode(', ', $primaryValidation['messages']);
        }
        
        return redirect()
            ->route('admin.special-bookings.show', $primaryBooking->id)
            ->with('warning', $warningMessage);
    }
}
