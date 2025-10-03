<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for API key in headers
        $apiKey = $request->header('RETREAT-API-KEY') ?? $request->header('Authorization');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required',
                'error_code' => 'MISSING_API_KEY'
            ], 401);
        }
        
        // Remove 'Bearer ' prefix if present
        $apiKey = str_replace('Bearer ', '', $apiKey);
        
        // Validate API key (you can store this in config or database)
        $validApiKey = config('app.api_key', 'mcrc_retreat_api_key_2025');
        
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'error_code' => 'INVALID_API_KEY'
            ], 401);
        }
        
        // Generate or retrieve session ID
        $sessionId = $request->header('RETREAT-SESSION-ID');
        
        if (!$sessionId) {
            $sessionId = 'api_session_' . Str::uuid();
            
            // Store session in cache for 24 hours
            Cache::put("api_session:{$sessionId}", [
                'created_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now()
            ], 60 * 24); // 24 hours
        } else {
            // Update last activity for existing session
            $sessionData = Cache::get("api_session:{$sessionId}");
            if ($sessionData) {
                $sessionData['last_activity'] = now();
                Cache::put("api_session:{$sessionId}", $sessionData, 60 * 24);
            }
        }
        
        // Add session ID to request
        $request->merge(['session_id' => $sessionId]);
        
        // Add session ID to response headers
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('RETREAT-SESSION-ID', $sessionId);
        }
        
        return $response;
    }
}
