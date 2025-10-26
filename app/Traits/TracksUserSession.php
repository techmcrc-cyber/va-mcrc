<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

trait TracksUserSession
{
    /**
     * Track user session activity
     * Collects same data as API session tracking
     */
    protected function trackSession(Request $request): string
    {
        // Get or create session ID
        $sessionId = session('frontend_session_id');
        
        if (!$sessionId) {
            // Create new session
            $sessionId = 'frontend_session_' . Str::uuid();
            session(['frontend_session_id' => $sessionId]);
            
            // Store session data in cache (same as API)
            Cache::put("frontend_session:{$sessionId}", [
                'created_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
                'page_views' => 1,
                'last_page' => $request->fullUrl()
            ], 60 * 24); // 24 hours
        } else {
            // Update existing session
            $sessionData = Cache::get("frontend_session:{$sessionId}");
            
            if ($sessionData) {
                $sessionData['last_activity'] = now();
                $sessionData['page_views'] = ($sessionData['page_views'] ?? 0) + 1;
                $sessionData['last_page'] = $request->fullUrl();
                
                // Extend session lifetime
                Cache::put("frontend_session:{$sessionId}", $sessionData, 60 * 24);
            }
        }
        
        return $sessionId;
    }
    
    /**
     * Get current session data
     */
    protected function getSessionData(): ?array
    {
        $sessionId = session('frontend_session_id');
        
        if (!$sessionId) {
            return null;
        }
        
        return Cache::get("frontend_session:{$sessionId}");
    }
}
