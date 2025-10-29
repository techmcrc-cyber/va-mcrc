<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RetreatAPIController;
use App\Traits\TracksUserSession;
use Illuminate\Http\Request;

class RetreatController extends Controller
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
        
        // Use the API controller to get all retreats
        $response = $this->retreatAPI->index($request);
        $responseData = json_decode($response->getContent(), true);

        $allRetreats = collect([]);
        
        if ($response->isSuccessful() && isset($responseData['data']['retreats'])) {
            $allRetreats = collect($responseData['data']['retreats']);
        }

        // Paginate the retreats collection (6 per page)
        $perPage = 6;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $currentPageItems = $allRetreats->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $retreats = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $allRetreats->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('frontend.retreats.index', compact('retreats'));
    }

    public function show(Request $request, $id)
    {
        // Track user session
        $this->trackSession($request);
        
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
