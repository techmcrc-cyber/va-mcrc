<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;
use Symfony\Component\HttpFoundation\Response;

class IdentifyOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get organization slug from URL path parameter
        $organizationSlug = $request->route('organization');
        
        if ($organizationSlug) {
            // Set URL default for organization parameter (for route generation)
            \Illuminate\Support\Facades\URL::defaults(['organization' => $organizationSlug]);
            
            // If slug is 'all', don't filter by organization
            if ($organizationSlug === 'all') {
                // No organization filtering - show all data
                config(['app.current_organization' => null]);
                view()->share('currentOrganization', null);
            } else {
                // Find organization by slug
                $organization = Organization::where('slug', $organizationSlug)
                    ->active()
                    ->verified()
                    ->first();
                
                if (!$organization) {
                    abort(404, 'Organization not found');
                }
                
                // Store organization globally
                $request->merge(['current_organization' => $organization]);
                config(['app.current_organization' => $organization]);
                view()->share('currentOrganization', $organization);
            }
        }
        
        return $next($request);
    }
}
