<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RetreatAPIController;
use Illuminate\Http\Request;

class RetreatController extends Controller
{
    protected $retreatAPI;

    public function __construct(RetreatAPIController $retreatAPI)
    {
        $this->retreatAPI = $retreatAPI;
    }

    public function index(Request $request)
    {
        // Use the API controller to get all retreats
        $response = $this->retreatAPI->index($request);
        $responseData = json_decode($response->getContent(), true);

        $retreats = collect([]);
        
        if ($response->isSuccessful() && isset($responseData['data']['retreats'])) {
            $retreats = collect($responseData['data']['retreats']);
        }

        return view('frontend.retreats.index', compact('retreats'));
    }

    public function show(Request $request, $id)
    {
        // Use the API controller to get retreat details
        $response = $this->retreatAPI->show($request, $id);
        $responseData = json_decode($response->getContent(), true);

        if (!$response->isSuccessful()) {
            abort(404, 'Retreat not found');
        }

        $retreat = $responseData['data'];
        $availableSeats = $retreat['availability']['available_spots'];

        return view('frontend.retreats.show', compact('retreat', 'availableSeats'));
    }
}
