<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Retreat;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get upcoming active retreats
        $upcomingRetreats = Retreat::active()
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();

        return view('frontend.home', compact('upcomingRetreats'));
    }
}
