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
            "{$bookingData['firstname']} {$bookingData['lastname']}"
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
                "{$participant['firstname']} {$participant['lastname']}"
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
        
        // Handle deleted participants
        if (isset($bookingData['deleted_participants']) && is_array($bookingData['deleted_participants'])) {
            Booking::whereIn('id', $bookingData['deleted_participants'])->delete();
        }
        
        // Update or create additional participants
        if (isset($bookingData['participants']) && is_array($bookingData['participants'])) {
            $existingParticipantIds = $booking->allParticipants()
                ->where('participant_number', '>', 1)
                ->pluck('id')
                ->toArray();
                
            $updatedParticipantIds = [];
            $participantNumber = 2;
            
            foreach ($bookingData['participants'] as $index => $participant) {
                $participantId = $participant['id'] ?? null;
                
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
                    'participant_number' => $participantNumber,
                    'updated_by' => $userId,
                ];
                
                if ($participantId && is_numeric($participantId) && in_array($participantId, $existingParticipantIds)) {
                    // Update existing participant
                    $participant = Booking::find($participantId);
                    if ($participant) {
                        $participant->update($participantData);
                        $updatedParticipantIds[] = $participantId;
                    }
                } else {
                    // Create new participant
                    $participantData['booking_id'] = $booking->booking_id;
                    $participantData['participant_number'] = $participantNumber;
                    $participantData['created_by'] = $userId;
                    $newParticipant = Booking::create($participantData);
                    $updatedParticipantIds[] = $newParticipant->id;
                }
                
                $participantNumber++;
            }
            
            // Delete removed participants
            $removedParticipants = array_diff($existingParticipantIds, $updatedParticipantIds);
            if (!empty($removedParticipants)) {
                Booking::whereIn('id', $removedParticipants)->delete();
            }
        } else {
            // No participants in the request, remove all additional participants
            $booking->allParticipants()
                ->where('participant_number', '>', 1)
                ->delete();
        }
        
        return redirect()
            ->route('admin.bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {        
        // Delete all participants with the same booking_id
        Booking::where('booking_id', $booking->booking_id)->delete();
        
        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
