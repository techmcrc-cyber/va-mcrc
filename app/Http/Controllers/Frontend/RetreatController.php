<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Retreat;
use Illuminate\Http\Request;

class RetreatController extends Controller
{
    public function index()
    {
        $retreats = Retreat::active()
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        return view('frontend.retreats.index', compact('retreats'));
    }

    public function show($id)
    {
        $retreat = Retreat::active()->findOrFail($id);

        // Calculate available seats
        $bookedSeats = $retreat->bookings()->active()->count();
        $availableSeats = $retreat->seats - $bookedSeats;

        return view('frontend.retreats.show', compact('retreat', 'availableSeats'));
    }
}
