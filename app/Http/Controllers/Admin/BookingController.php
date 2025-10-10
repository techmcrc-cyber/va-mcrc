<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use App\Imports\BookingsImport;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.bookings.active');
    }

    public function active(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with(['retreat', 'creator'])
                ->where('participant_number', 1)
                ->where('is_active', true)
                ->whereHas('retreat', function($q) {
                    $q->where('end_date', '>=', now());
                });
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('booking_id', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('whatsapp_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('retreat', function($q) use ($search) {
                          $q->where('title', 'like', "%{$search}%");
                      });
                });
            }
            
            // Handle retreat filter
            if ($request->has('retreat_filter') && !empty($request->retreat_filter)) {
                $query->whereHas('retreat', function($q) use ($request) {
                    $q->where('title', $request->retreat_filter);
                });
            }
            
            // Handle status filter
            if ($request->has('status_filter') && !empty($request->status_filter)) {
                $status = $request->status_filter;
                
                if ($status === 'confirmed') {
                    $query->where('is_active', 1)
                          ->where(function($q) {
                              $q->whereNull('flag')
                                ->orWhere('flag', '');
                          });
                } elseif ($status === 'pending') {
                    $query->where('is_active', 2);
                } else {
                    $query->where(function($q) use ($status) {
                        $q->where('flag', 'LIKE', "%{$status}%");
                    });
                }
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['booking_id', 'retreat_id', 'firstname', 'additional_participants', 'flag'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                } else {
                    $query->latest();
                }
            } else {
                $query->latest();
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $bookings = $query->offset($start)->limit($limit)->get();
            
            $data = $this->formatBookingsData($bookings);
            
            $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval(Booking::where('participant_number', 1)
                    ->where('is_active', true)
                    ->whereHas('retreat', function($q) {
                        $q->where('end_date', '>=', now());
                    })->count()),
                "recordsFiltered" => intval($totalData),
                "data"            => $data
            ];
            
            return response()->json($json_data);
        }

        // Get active retreats for filter dropdown
        $retreats = Retreat::where('end_date', '>=', now()->toDateString())
            ->whereHas('bookings', function($query) {
                $query->where('participant_number', 1)
                      ->where('is_active', true);
            })
            ->orderBy('start_date')
            ->get();
            
        return view('admin.bookings.active', compact('retreats'));
    }

    public function archive(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with(['retreat', 'creator'])
                ->where('participant_number', 1)
                ->where('is_active', true)
                ->whereHas('retreat', function($q) {
                    $q->where('end_date', '<', now()->toDateString());
                });
            
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('booking_id', 'like', "%{$search}%")
                      ->orWhere('firstname', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('whatsapp_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('retreat', function($q) use ($search) {
                          $q->where('title', 'like', "%{$search}%");
                      });
                });
            }
            
            // Handle retreat filter
            if ($request->has('retreat_filter') && !empty($request->retreat_filter)) {
                $query->whereHas('retreat', function($q) use ($request) {
                    $q->where('title', $request->retreat_filter);
                });
            }
            
            // Handle status filter
            if ($request->has('status_filter') && !empty($request->status_filter)) {
                $status = $request->status_filter;
                
                if ($status === 'confirmed') {
                    $query->where('is_active', 1)
                          ->where(function($q) {
                              $q->whereNull('flag')
                                ->orWhere('flag', '');
                          });
                } elseif ($status === 'pending') {
                    $query->where('is_active', 2);
                } else {
                    $query->where(function($q) use ($status) {
                        $q->where('flag', 'LIKE', "%{$status}%");
                    });
                }
            }
            
            // Handle sorting
            if ($request->has('order')) {
                $column = $request->input('order.0.column');
                $dir = $request->input('order.0.dir');
                $columns = ['booking_id', 'retreat_id', 'firstname', 'additional_participants', 'flag'];
                
                if (isset($columns[$column])) {
                    $query->orderBy($columns[$column], $dir);
                } else {
                    $query->latest();
                }
            } else {
                $query->latest();
            }
            
            $totalData = $query->count();
            $limit = $request->input('length', 25);
            $start = $request->input('start', 0);
            
            $bookings = $query->offset($start)->limit($limit)->get();
            
            $data = $this->formatBookingsData($bookings, true);
            
            $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval(Booking::where('participant_number', 1)
                    ->where('is_active', true)
                    ->whereHas('retreat', function($q) {
                        $q->where('end_date', '<', now()->toDateString());
                    })->count()),
                "recordsFiltered" => intval($totalData),
                "data"            => $data
            ];
            
            return response()->json($json_data);
        }
        
        // Get archived retreats for filter dropdown
        $retreats = Retreat::where('end_date', '<', now()->toDateString())
            ->whereHas('bookings', function($query) {
                $query->where('participant_number', 1)
                      ->where('is_active', true);
            })
            ->orderBy('start_date', 'desc')
            ->get();
            
        return view('admin.bookings.archive', compact('retreats'));
    }

    private function formatBookingsData($bookings, $isArchive = false)
    {
        $data = [];
        foreach ($bookings as $booking) {
            $nestedData = [];
            $nestedData['booking_id'] = $booking->booking_id;
            $nestedData['retreat'] = $booking->retreat->title;
            
            // Guest info with flag
            $guestInfo = '<div class="guest-info">';
            $guestInfo .= '<div class="guest-name">';
            $guestInfo .= '<strong>' . e($booking->firstname . ' ' . $booking->lastname) . '</strong>';
            if ($booking->flag) {
                $guestInfo .= ' <span class="badge bg-warning ms-1" data-toggle="tooltip" title="' . e($booking->flag) . '">';
                $guestInfo .= '<i class="fas fa-exclamation-triangle"></i></span>';
            }
            $guestInfo .= '</div>';
            
            $guestInfo .= '<div class="guest-contact mt-1">';
            if ($booking->whatsapp_number) {
                $guestInfo .= '<small class="text-muted d-block"><i class="fas fa-phone-alt me-1"></i>' . e($booking->whatsapp_number) . '</small>';
            }
            if ($booking->email) {
                $guestInfo .= '<small class="text-muted d-block"><i class="fas fa-envelope me-1"></i>' . e($booking->email) . '</small>';
            }
            $guestInfo .= '</div></div>';
            
            $nestedData['guest_info'] = $guestInfo;
            
            // Participants
            $participants = '<span class="badge bg-primary">' . ($booking->additional_participants + 1) . '</span>';
            if ($booking->additional_participants > 0) {
                $participants .= '<br><small class="text-muted">(+' . $booking->additional_participants . ')</small>';
            }
            $nestedData['participants'] = $participants;
            
            // Status
            $status = '';
            if ($booking->flag) {
                $flags = explode(',', $booking->flag);
                foreach ($flags as $flag) {
                    $status .= '<div class="mb-1">';
                    $status .= '<span class="badge bg-warning">' . e(Str::title(str_replace('_', ' ', trim($flag)))) . '</span>';
                    $status .= '</div>';
                }
            } else {
                $status = '<span class="badge bg-success">Confirmed</span>';
            }
            $nestedData['status'] = $status;
            
            // Actions
            $actions = '<div class="action-buttons">';
            
            if ($isArchive) {
                // For archive list, show only View button
                $actions .= '<div class="btn-row">';
                $actions .= '<a href="' . route('admin.bookings.show', $booking->id) . '" class="btn btn-sm btn-info" title="View">';
                $actions .= '<i class="fas fa-eye"></i></a>';
                $actions .= '</div>';
            } else {
                // For active list, show all buttons
                $actions .= '<div class="btn-row mb-1">';
                $actions .= '<a href="' . route('admin.bookings.show', $booking->id) . '" class="btn btn-sm btn-info me-1" title="View">';
                $actions .= '<i class="fas fa-eye"></i></a> ';
                $actions .= '<a href="' . route('admin.bookings.edit', $booking->id) . '" class="btn btn-sm btn-primary" title="Edit">';
                $actions .= '<i class="fas fa-edit"></i></a>';
                $actions .= '</div>';
                $actions .= '<div class="btn-row">';
                $actions .= '<form action="' . route('admin.bookings.destroy', $booking->id) . '" method="POST" class="d-inline w-100">';
                $actions .= csrf_field();
                $actions .= method_field('DELETE');
                $actions .= '<button type="submit" class="btn btn-sm btn-danger w-100" title="Cancel Booking" ';
                $actions .= 'onclick="return confirm(\'Are you sure you want to cancel this booking? This will deactivate all participants in this booking.\')">';
                $actions .= '<i class="fas fa-ban"></i></button></form>';
                $actions .= '</div>';
            }
            
            $actions .= '</div>';
            
            $nestedData['actions'] = $actions;
            
            $data[] = $nestedData;
        }
        
        return $data;
    }

    public function create()
    {
        $retreats = Retreat::where('is_active', true)
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();
            
        return view('admin.bookings.create', compact('retreats'));
    }

    public function store(BookingRequest $request)
    {
        $bookingData = $request->validated();
        $retreat = \App\Models\Retreat::findOrFail($bookingData['retreat_id']);
        $validationService = new \App\Services\CriteriaValidationService();
        
        // Validate primary participant with STRICT mode
        $primaryValidation = $validationService->validateWithRecurrentCheck(
            $bookingData,
            $retreat->criteria,
            true // Strict mode - booking fails if criteria not met
        );
        
        if (!$primaryValidation['valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['criteria' => 'Primary participant does not meet retreat criteria: ' . implode(', ', $primaryValidation['messages'])]);
        }
        
        // Validate additional participants
        $participants = $bookingData['participants'] ?? [];
        foreach ($participants as $index => $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue;
            }
            
            $participantValidation = $validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                true // Strict mode
            );
            
            if (!$participantValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(["participants.{$index}.criteria" => implode(', ', $participantValidation['messages'])]);
            }
        }
        
        // All validations passed, create bookings
        $bookingId = Booking::generateBookingId();
        $userId = Auth::id();
        
        // Create primary booking
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
            'flag' => $primaryValidation['flag_string'], // Only RECURRENT_BOOKING flag if applicable
            'participant_number' => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
            'is_active' => true,
        ]);
        
        // Create additional participants
        $participantNumber = 2;
        foreach ($participants as $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue;
            }
            
            $participantValidation = $validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                true
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
                'married' => $participant['married'] ?? null,
                'congregation' => $participant['congregation'] ?? null,
                'participant_number' => $participantNumber,
                'flag' => $participantValidation['flag_string'],
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
        
        return redirect()
            ->route('admin.bookings.show', $primaryBooking->id)
            ->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $allParticipants = $booking->allParticipants();
        
        // Update the additional_participants count to reflect only active participants
        $booking->additional_participants = $allParticipants->where('participant_number', '>', 1)->count();
        
        return view('admin.bookings.show', compact('booking', 'allParticipants'));
    }

    public function edit(Booking $booking)
    {
        if ($booking->participant_number !== 1) {
            return redirect()
                ->route('admin.bookings.edit', $booking->booking->where('participant_number', 1)->first())
                ->with('warning', 'Please edit the primary booking to modify all participants.');
        }
        
        $retreats = Retreat::where(function ($query) {
                $query->where('is_active', true)
                      ->where('end_date', '>=', now()->toDateString());
            })
            ->orWhere('id', $booking->retreat_id) // Include current retreat even if inactive
            ->orderBy('start_date')
            ->get();
            
        $allParticipants = $booking->allParticipants();
        
        return view('admin.bookings.edit', compact('booking', 'retreats', 'allParticipants'));
    }

    public function update(BookingRequest $request, Booking $booking)
    {        
        if ($booking->participant_number !== 1) {
            return redirect()
                ->route('admin.bookings.edit', $booking->booking->where('participant_number', 1)->first())
                ->with('warning', 'Please edit the primary booking to modify all participants.');
        }
        
        $bookingData = $request->validated();
        $retreat = \App\Models\Retreat::findOrFail($bookingData['retreat_id']);
        $validationService = new \App\Services\CriteriaValidationService();
        $userId = Auth::id();
        
        // Validate primary participant with STRICT mode
        $primaryValidation = $validationService->validateWithRecurrentCheck(
            $bookingData,
            $retreat->criteria,
            true, // Strict mode - booking fails if criteria not met
            $booking->booking_id
        );
        
        if (!$primaryValidation['valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['criteria' => 'Primary participant does not meet retreat criteria: ' . implode(', ', $primaryValidation['messages'])]);
        }
        
        // Validate additional participants with STRICT mode
        $participants = $bookingData['participants'] ?? [];
        foreach ($participants as $index => $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue;
            }
            
            $participantValidation = $validationService->validateWithRecurrentCheck(
                $participant,
                $retreat->criteria,
                true, // Strict mode
                $booking->booking_id
            );
            
            if (!$participantValidation['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(["participants.{$index}.criteria" => implode(', ', $participantValidation['messages'])]);
            }
        }
        
        // All validations passed, update primary booking
        $booking->update([
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
            'flag' => $primaryValidation['flag_string'], // Only RECURRENT_BOOKING flag if applicable
            'updated_by' => $userId,
        ]);
        
        // Handle deleted participants
        if (isset($bookingData['deleted_participants']) && is_array($bookingData['deleted_participants'])) {
            Booking::whereIn('id', $bookingData['deleted_participants'])->delete();
        }
        
        // Update or create additional participants using booking_id and participant_number
        if (isset($bookingData['participants']) && is_array($bookingData['participants'])) {
            // First, mark all current additional participants as inactive
            // We'll reactivate the ones that are still in the form
            Booking::where('booking_id', $booking->booking_id)
                ->where('participant_number', '>', 1)
                ->update(['is_active' => false]);
            
            $participantNumber = 2; // Start from 2 for additional participants
            
            foreach ($bookingData['participants'] as $index => $participant) {
                // Skip empty participant entries
                if (empty($participant['firstname']) && empty($participant['lastname'])) {
                    continue;
                }
                
                // Validate participant (already validated above, get validation result again)
                $participantValidation = $validationService->validateWithRecurrentCheck(
                    $participant,
                    $retreat->criteria,
                    true,
                    $booking->booking_id
                );
                
                $participantData = [
                    'retreat_id' => $bookingData['retreat_id'],
                    'firstname' => $participant['firstname'],
                    'lastname' => $participant['lastname'],
                    'whatsapp_number' => $participant['whatsapp_number'],
                    'age' => $participant['age'],
                    'email' => $participant['email'],
                    'address' => $bookingData['address'],
                    'gender' => $participant['gender'],
                    'married' => $participant['married'] ?? null,
                    'congregation' => $participant['congregation'] ?? null,
                    'city' => $bookingData['city'],
                    'state' => $bookingData['state'],
                    'diocese' => $bookingData['diocese'] ?? null,
                    'parish' => $bookingData['parish'] ?? null,
                    'emergency_contact_name' => $bookingData['emergency_contact_name'],
                    'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
                    'special_remarks' => $bookingData['special_remarks'] ?? null,
                    'flag' => $participantValidation['flag_string'], // Only RECURRENT_BOOKING flag if applicable
                    'updated_by' => $userId,
                ];
                
                // Check if participant with this booking_id and participant_number exists
                $existingParticipant = Booking::where('booking_id', $booking->booking_id)
                    ->where('participant_number', $participantNumber)
                    ->first();
                
                if ($existingParticipant) {
                    // Update existing participant
                    $existingParticipant->update($participantData);
                    $existingParticipant->update(['is_active' => true]); // Reactivate
                } else {
                    // Create new participant
                    $participantData['booking_id'] = $booking->booking_id;
                    $participantData['participant_number'] = $participantNumber;
                    $participantData['created_by'] = $userId;
                    $participantData['is_active'] = true;
                    
                    Booking::create($participantData);
                }
                
                $participantNumber++;
            }
        } else {
            // No participants in the request, mark all additional participants as inactive
            Booking::where('booking_id', $booking->booking_id)
                ->where('participant_number', '>', 1)
                ->update(['is_active' => false]);
        }
        
        return redirect()
            ->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {        
        // Mark all participants with the same booking_id as inactive
        Booking::where('booking_id', $booking->booking_id)->update(['is_active' => false]);
        
        // Determine redirect based on retreat end date (before today)
        $isArchived = $booking->retreat->end_date->toDateString() < now()->toDateString();
        $redirectRoute = $isArchived ? 'admin.bookings.archive' : 'admin.bookings.active';
        
        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Booking cancelled successfully.');
    }
    
    /**
     * Cancel an individual participant (mark as inactive).
     */
    public function cancelParticipant(Booking $participant)
    {
        // Check if this is a primary participant
        if ($participant->participant_number === 1) {
            // If primary participant, cancel entire booking
            return $this->destroy($participant);
        }
        
        // Mark only this participant as inactive
        $participant->update(['is_active' => false]);
        
        // Get the primary booking to redirect back to
        $primaryBooking = Booking::where('booking_id', $participant->booking_id)
            ->where('participant_number', 1)
            ->first();
        
        return redirect()
            ->route('admin.bookings.show', $primaryBooking->id)
            ->with('success', 'Participant cancelled successfully.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        $retreats = Retreat::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
            
        return view('admin.bookings.import', compact('retreats'));
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        return Excel::download(new BookingsExport(collect(), true), 'booking_import_template.xlsx');
    }

    /**
     * Preview import data
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'retreat_id' => 'required|exists:retreats,id'
        ]);

        try {
            $import = new BookingsImport($request->retreat_id, true); // Preview mode
            Excel::import($import, $request->file('import_file'));
            
            $previewData = $import->getPreviewData();
            
            // Store import data in session for confirmation
            Session::put('import_preview_data', [
                'data' => $previewData,
                'retreat_id' => $request->retreat_id,
                'file_name' => $request->file('import_file')->getClientOriginalName()
            ]);
            
            $retreat = Retreat::find($request->retreat_id);
            
            return view('admin.bookings.import-preview', compact('previewData', 'retreat'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['import_file' => 'Error processing file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Confirm and process import
     */
    public function confirmImport(Request $request)
    {
        $previewData = Session::get('import_preview_data');
        
        if (!$previewData) {
            return redirect()->route('admin.bookings.import')
                ->withErrors(['general' => 'No import data found. Please upload file again.']);
        }

        try {
            $import = new BookingsImport($previewData['retreat_id'], false); // Live mode
            $import->setPreviewData($previewData['data']);
            $import->processImport();
            
            $results = $import->getImportResults();
            
            Session::forget('import_preview_data');
            
            return redirect()->route('admin.bookings.active')
                ->with('success', "Import completed. {$results['success']} bookings imported successfully. {$results['errors']} errors encountered.");
                
        } catch (\Exception $e) {
            return redirect()->route('admin.bookings.import')
                ->withErrors(['general' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show export form
     */
    public function exportForm()
    {
        $retreats = Retreat::withCount(['bookings' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('start_date', 'desc')
            ->get();
        
        return view('admin.bookings.export', compact('retreats'));
    }

    /**
     * Process export
     */
    public function processExport(Request $request)
    {
        $request->validate([
            'retreat_id' => 'nullable|exists:retreats,id'
        ]);

        $query = Booking::with(['retreat'])
            ->where('is_active', true)
            ->orderBy('booking_id')
            ->orderBy('participant_number');

        if ($request->retreat_id) {
            $query->where('retreat_id', $request->retreat_id);
            $retreat = Retreat::find($request->retreat_id);
            $filename = 'bookings_' . Str::slug($retreat->title) . '_' . now()->format('Y-m-d') . '.xlsx';
        } else {
            $filename = 'all_bookings_' . now()->format('Y-m-d') . '.xlsx';
        }

        $bookings = $query->get();

        return Excel::download(new BookingsExport($bookings), $filename);
    }
}
