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
        // Get organization slug from route parameter
        $organizationSlug = $request->route('organization');
        
        if ($organizationSlug) {
            // Find organization by slug
            $organization = Organization::where('slug', $organizationSlug)
                ->active()
                ->verified()
                ->first();
            
            if (!$organization) {
                abort(404, 'Organization not found');
            }
            
            // Store organization in request for easy access
            $request->merge(['current_organization' => $organization]);
            
            // Also store in config for global access
            config(['app.current_organization' => $organization]);
            
            // Share with views
            view()->share('currentOrganization', $organization);
        }
        
        return $next($request);
    }
}
