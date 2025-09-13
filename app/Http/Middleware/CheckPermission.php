<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Super admin has all permissions
        if ($user->role && $user->role->is_super_admin) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', Response::HTTP_FORBIDDEN);
            }
            
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
