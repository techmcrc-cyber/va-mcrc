<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RetreatAPIController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $retreatAPI;

    public function __construct(RetreatAPIController $retreatAPI)
    {
        $this->retreatAPI = $retreatAPI;
    }

    public function index(Request $request)
    {
        echo "coming soon";
        die();
        // Use the API controller to get retreats
        $response = $this->retreatAPI->index($request);
        $responseData = json_decode($response->getContent(), true);

        $upcomingRetreats = collect([]);
        
        if ($response->isSuccessful() && isset($responseData['data']['retreats'])) {
            // Take only first 6 for homepage
            $upcomingRetreats = collect($responseData['data']['retreats'])->take(6);
        }

        return view('frontend.home', compact('upcomingRetreats'));
    }
}
