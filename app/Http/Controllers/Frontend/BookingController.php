<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BookingAPIController;
use App\Models\Retreat;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingAPI;

    public function __construct(BookingAPIController $bookingAPI)
    {
        $this->bookingAPI = $bookingAPI;
    }

    public function create(Request $request)
    {
        $retreatId = $request->query('retreat_id');
        $retreat = null;

        if ($retreatId) {
            $retreat = Retreat::active()
                ->where('id', $retreatId)
                ->whereDate('end_date', '>=', now()->toDateString())
                ->first();
        }

        $retreats = Retreat::with('criteriaRelation')
            ->active()
            ->whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc')
            ->get();

        return view('frontend.booking.register', compact('retreats', 'retreat'));
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

    public function checkStatusForm()
    {
        return view('frontend.booking.check-status');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|string',
            'whatsapp_number' => 'required|numeric|digits:10',
        ]);

        // Create a new request with query parameters for the API
        $apiRequest = Request::create('', 'GET', [
            'booking_id' => $request->booking_id,
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        // Use the API controller directly (same application)
        $response = $this->bookingAPI->show($apiRequest);
        $responseData = json_decode($response->getContent(), true);

        if ($response->isSuccessful()) {
            $bookingDetails = $responseData['data'];
            return view('frontend.booking.status', compact('bookingDetails'));
        }

        return back()
            ->withInput()
            ->withErrors(['error' => $responseData['message'] ?? 'Booking not found. Please check your details.']);
    }
}
