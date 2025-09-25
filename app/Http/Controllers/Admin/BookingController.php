<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Retreat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['retreat', 'creator'])
            ->where('participant_number', 1) // Only show primary bookings in the list
            ->where('is_active', true) // Only show active bookings
            ->latest()
            ->get();
            
        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $retreats = Retreat::where('is_active', true)
            ->where('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
            
        return view('admin.bookings.create', compact('retreats'));
    }

    public function store(BookingRequest $request)
    {
        
        $bookingData = $request->validated();
        $bookingId = Booking::generateBookingId();
        $userId = Auth::id();
        $flag = null;
        
        // Check for past bookings
        if (Booking::hasAttendedInPastYear(
            $bookingData['whatsapp_number'],
            $bookingData['firstname'],
            $bookingData['lastname']
        )) {
            $flag = 'RECURRENT_BOOKING';
        }
        
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
            'city' => $bookingData['city'],
            'state' => $bookingData['state'],
            'diocese' => $bookingData['diocese'] ?? null,
            'parish' => $bookingData['parish'] ?? null,
            'congregation' => $bookingData['congregation'] ?? null,
            'emergency_contact_name' => $bookingData['emergency_contact_name'],
            'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
            'additional_participants' => $bookingData['additional_participants'],
            'special_remarks' => $bookingData['special_remarks'] ?? null,
            'flag' => $flag,
            'participant_number' => 1,
            'created_by' => $userId,
            'updated_by' => $userId,
            'is_active' => true,
        ]);
        
        // Check criteria for primary booking
        if (!$primaryBooking->meetsRetreatCriteria()) {
            $primaryBooking->flag = $primaryBooking->flag 
                ? $primaryBooking->flag . ',CRITERIA_FAILED' 
                : 'CRITERIA_FAILED';
            $primaryBooking->save();
        }
        
        // Create additional participants if any
        $participants = $bookingData['participants'] ?? [];
        $participantNumber = 2; // Start from 2 for additional participants
        
        foreach ($participants as $participant) {
            if (empty($participant['firstname']) && empty($participant['lastname'])) {
                continue; // Skip empty participant entries
            }
            
            $participantFlag = null;
            
            if (Booking::hasAttendedInPastYear(
                $participant['whatsapp_number'] ?? '',
                $participant['firstname'] ?? '',
                $participant['lastname'] ?? ''
            )) {
                $participantFlag = 'RECURRENT_BOOKING';
            }
            
            $participantBooking = Booking::create([
                'booking_id' => $bookingId,
                'retreat_id' => $bookingData['retreat_id'],
                'firstname' => $participant['firstname'] ?? '',
                'lastname' => $participant['lastname'] ?? '',
                'whatsapp_number' => $participant['whatsapp_number'] ?? '',
                'age' => $participant['age'] ?? null,
                'email' => $participant['email'] ?? null,
                'gender' => $participant['gender'] ?? 'other',
                'participant_number' => $participantNumber,
                'flag' => $participantFlag,
                'created_by' => $userId,
                'updated_by' => $userId,
                'address' => $participant['address'] ?? '',
                'city' => $participant['city'] ?? '',
                'state' => $participant['state'] ?? '',
                'emergency_contact_name' => $bookingData['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $bookingData['emergency_contact_phone'] ?? '',
                'is_active' => true,
            ]);
            
            // Check criteria for participant
            if (!$participantBooking->meetsRetreatCriteria()) {
                $participantBooking->flag = $participantBooking->flag 
                    ? $participantBooking->flag . ',CRITERIA_FAILED' 
                    : 'CRITERIA_FAILED';
                $participantBooking->save();
            }
            
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
                      ->where('end_date', '>=', now());
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
        $userId = Auth::id();
        
        // Update primary booking
        $booking->update([
            'retreat_id' => $bookingData['retreat_id'],
            'firstname' => $bookingData['firstname'],
            'lastname' => $bookingData['lastname'],
            'whatsapp_number' => $bookingData['whatsapp_number'],
            'age' => $bookingData['age'],
            'email' => $bookingData['email'],
            'address' => $bookingData['address'],
            'gender' => $bookingData['gender'],
            'city' => $bookingData['city'],
            'state' => $bookingData['state'],
            'diocese' => $bookingData['diocese'] ?? null,
            'parish' => $bookingData['parish'] ?? null,
            'congregation' => $bookingData['congregation'] ?? null,
            'emergency_contact_name' => $bookingData['emergency_contact_name'],
            'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
            'additional_participants' => $bookingData['additional_participants'],
            'special_remarks' => $bookingData['special_remarks'] ?? null,
            'updated_by' => $userId,
        ]);
        
        // Re-evaluate flags for primary booking after update
        $flag = null;
        
        // Check for recurrent booking (exclude current booking from check)
        if (Booking::hasAttendedInPastYear(
            $bookingData['whatsapp_number'],
            $bookingData['firstname'],
            $bookingData['lastname'],
            $booking->booking_id
        )) {
            $flag = 'RECURRENT_BOOKING';
        }
        
        // Check criteria for primary booking
        if (!$booking->fresh()->meetsRetreatCriteria()) {
            $flag = $flag ? $flag . ',CRITERIA_FAILED' : 'CRITERIA_FAILED';
        }
        
        // Update flag for primary booking
        $booking->update(['flag' => $flag]);
        
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
                
                $participantData = [
                    'retreat_id' => $bookingData['retreat_id'],
                    'firstname' => $participant['firstname'],
                    'lastname' => $participant['lastname'],
                    'whatsapp_number' => $participant['whatsapp_number'],
                    'age' => $participant['age'],
                    'email' => $participant['email'],
                    'address' => $bookingData['address'],
                    'gender' => $participant['gender'],
                    'city' => $bookingData['city'],
                    'state' => $bookingData['state'],
                    'diocese' => $bookingData['diocese'] ?? null,
                    'parish' => $bookingData['parish'] ?? null,
                    'congregation' => $bookingData['congregation'] ?? null,
                    'emergency_contact_name' => $bookingData['emergency_contact_name'],
                    'emergency_contact_phone' => $bookingData['emergency_contact_phone'],
                    'special_remarks' => $bookingData['special_remarks'] ?? null,
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
                    
                    $participantBooking = $existingParticipant;
                } else {
                    // Create new participant
                    $participantData['booking_id'] = $booking->booking_id;
                    $participantData['participant_number'] = $participantNumber;
                    $participantData['created_by'] = $userId;
                    $participantData['is_active'] = true;
                    
                    $participantBooking = Booking::create($participantData);
                }
                
                // Re-evaluate flags for participant after update/create
                $participantFlag = null;
                
                // Check for recurrent booking (exclude current booking from check)
                if (Booking::hasAttendedInPastYear(
                    $participant['whatsapp_number'],
                    $participant['firstname'],
                    $participant['lastname'],
                    $booking->booking_id
                )) {
                    $participantFlag = 'RECURRENT_BOOKING';
                }
                
                // Check criteria for participant
                if (!$participantBooking->fresh()->meetsRetreatCriteria()) {
                    $participantFlag = $participantFlag ? $participantFlag . ',CRITERIA_FAILED' : 'CRITERIA_FAILED';
                }
                
                // Update flag for participant
                $participantBooking->update(['flag' => $participantFlag]);
                
                $participantNumber++;
            }
        } else {
            // No participants in the request, mark all additional participants as inactive
            $booking->allParticipants()
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
        
        return redirect()
            ->route('admin.bookings.index')
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
}
