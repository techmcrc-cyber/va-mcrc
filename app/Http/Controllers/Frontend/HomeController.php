<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RetreatAPIController;
use App\Traits\TracksUserSession;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use TracksUserSession;
    
    protected $retreatAPI;

    public function __construct(RetreatAPIController $retreatAPI)
    {
        $this->retreatAPI = $retreatAPI;
    }

    public function index(Request $request)
    {
        // Track user session
        $this->trackSession($request);
        
        // Use the API controller to get retreats
        $response = $this->retreatAPI->index($request);
        $responseData = json_decode($response->getContent(), true);

        $upcomingRetreats = collect([]);
        
        if ($response->isSuccessful() && isset($responseData['data']['retreats'])) {
            // Sort by end_date ascending and take first 3 (ending soonest)
            $upcomingRetreats = collect($responseData['data']['retreats'])
                ->sortBy('end_date')
                ->take(3)
                ->values();
        }

        return view('frontend.home', compact('upcomingRetreats'));
    }
}
