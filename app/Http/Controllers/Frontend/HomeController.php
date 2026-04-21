<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\RetreatAPIController;
use App\Traits\TracksUserSession;
use App\Models\Leader;
use App\Helpers\OrganizationHelper;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use TracksUserSession;
    
    protected $retreatAPI;

    public function __construct(RetreatAPIController $retreatAPI)
    {
        $this->retreatAPI = $retreatAPI;
    }

    public function index(Request $request, $organization)
    {
        // Track user session
        $this->trackSession($request);
        
        // Use the API controller to get retreats
        $response = $this->retreatAPI->index($request);
        $responseData = json_decode($response->getContent(), true);

        $upcomingRetreats = collect([]);
        
        if ($response->isSuccessful() && isset($responseData['data']['retreats'])) {
            // Sort by featured first (desc), then by start_date ascending
            $upcomingRetreats = collect($responseData['data']['retreats'])
                ->sortBy([
                    ['is_featured', 'desc'],
                    ['start_date', 'asc']
                ])
                ->take(3)
                ->values();
        }

        // Load leaders
        // Always show 3 default leaders (organization_id = null)
        $defaultLeaders = Leader::where('organization_id', null)
            ->active()
            ->ordered()
            ->take(3)
            ->get();

        // Load organization-specific leaders
        $organizationLeaders = collect([]);
        $organizationId = OrganizationHelper::currentId();
        
        if ($organizationId) {
            // If viewing a specific organization, show that organization's leaders
            $organizationLeaders = Leader::with('organization')
                ->where('organization_id', $organizationId)
                ->active()
                ->ordered()
                ->get();
        } else {
            // If viewing /all/, show leaders from ALL organizations
            $organizationLeaders = Leader::with('organization')
                ->whereNotNull('organization_id')
                ->active()
                ->ordered()
                ->get();
        }

        return view('frontend.home', compact('upcomingRetreats', 'defaultLeaders', 'organizationLeaders'));
    }
}
