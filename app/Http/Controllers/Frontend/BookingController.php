<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BookingAPIController;
use App\Traits\TracksUserSession;
use App\Models\Retreat;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use TracksUserSession;
    
    protected $bookingAPI;

    public function __construct(BookingAPIController $bookingAPI)
    {
        $this->bookingAPI = $bookingAPI;
    }

    public function create(Request $request)
    {
        // Track user session
        $this->trackSession($request);
        
        $retreatId = $request->query('retreat_id');
        $retreat = null;

        if ($retreatId) {
            $retreat = Retreat::active()
                ->where('id', $retreatId)
                ->whereDate('end_date', '>=', now()->toDateString())
                ->first();
        }

        $retreats = Retreat::with(['criteriaRelation', 'bookings' => function($query) {
                $query->where('is_active', true);
            }])
            ->active()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->get()
            ->filter(function ($retreat) {
                $bookedSeats = $retreat->bookings->count();
                return $bookedSeats < $retreat->seats; // Only show retreats with available seats
            });

        // Get max participants from database settings
        $maxAdditionalMembers = \App\Models\Setting::get('MAX_ADDITIONAL_MEMBERS', 3);
        $maxParticipants = $maxAdditionalMembers + 1; // +1 for primary participant

        return view('frontend.booking.register', compact('retreats', 'retreat', 'maxParticipants'));
    }

    public function store(Request $request)
    {
        // Use the API controller directly (same application)
        $response = $this->bookingAPI->store($request);
        $responseData = json_decode($response->getContent(), true);

        if ($response->isSuccessful()) {
            return redirect()
                ->route('booking.success')
                ->with('booking_data', $responseData['data']);
        }

        // Handle validation errors
        if (isset($responseData['errors'])) {
            return back()
                ->withInput()
                ->withErrors($responseData['errors']);
        }

        return back()
            ->withInput()
            ->withErrors(['error' => $responseData['message'] ?? 'Booking failed. Please try again.']);
    }

    public function success()
    {
        $bookingData = session('booking_data');
        
        if (!$bookingData) {
            return redirect()->route('home');
        }

        return view('frontend.booking.success', compact('bookingData'));
    }

    public function checkStatusForm(Request $request)
    {
        // Track user session
        $this->trackSession($request);
        
        return view('frontend.booking.check-status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|string',
            'whatsapp_number' => 'required|numeric|digits:10',
        ]);

        // Generate session ID for this booking check
        $sessionId = 'session_' . time() . '_' . uniqid();

        // Create a new request with query parameters for the API
        $apiRequest = Request::create('', 'GET', [
            'booking_id' => $request->booking_id,
            'whatsapp_number' => $request->whatsapp_number,
            'session_id' => $sessionId,
        ]);

        // Use the API controller directly (same application)
        $response = $this->bookingAPI->show($apiRequest);
        $responseData = json_decode($response->getContent(), true);

        if ($response->isSuccessful()) {
            $bookingDetails = $responseData['data'];
            
            // Store session data for cancellation
            session([
                'booking_session_id' => $sessionId,
                'booking_context' => [
                    'booking_id' => $request->booking_id,
                    'whatsapp_number' => $request->whatsapp_number,
                ]
            ]);
            
            return view('frontend.booking.status', compact('bookingDetails', 'sessionId'));
        }

        return back()
            ->withInput()
            ->withErrors(['error' => $responseData['message'] ?? 'Booking not found. Please check your details.']);
    }

    public function cancelParticipant(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|integer|min:1|max:4',
        ]);

        // Get booking context from session
        $bookingContext = session('booking_context');
        $sessionId = session('booking_session_id');

        if (!$bookingContext || !$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please check your booking status again.'
            ], 400);
        }

        // Create API request with proper headers
        $apiRequest = Request::create(
            "/api/bookings/{$bookingContext['booking_id']}/cancel",
            'PATCH',
            [
                'serial_number' => $request->serial_number,
                'session_id' => $sessionId,
            ]
        );

        // Call API controller with authentication
        $response = $this->bookingAPI->cancel($apiRequest, $bookingContext['booking_id']);
        $responseData = json_decode($response->getContent(), true);

        if ($response->isSuccessful()) {
            // Clear session data
            session()->forget(['booking_session_id', 'booking_context']);
            
            return response()->json([
                'success' => true,
                'message' => 'Cancellation successful',
                'data' => $responseData['data'] ?? null
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $responseData['message'] ?? 'Cancellation failed'
        ], $response->status());
    }
}
