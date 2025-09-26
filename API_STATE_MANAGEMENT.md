# API State Management Documentation
**Retreat Management System - Session ID & State Tracking**

---

## 📋 **Overview**

The Retreat Management System API implements a sophisticated session-based state management system that tracks client interactions across multiple API requests. Unlike traditional web sessions, this system is designed specifically for API clients including mobile apps, third-party integrations, and web applications.

---

## 🔧 **Implementation Details**

### **Core Components:**
- **Middleware**: `App\Http\Middleware\ApiAuthentication`
- **Storage**: Laravel Cache (Redis/File-based)
- **Session Duration**: 24 hours
- **Session ID Format**: `api_session_{UUID}`

---

## 🎯 **How It Works**

### **1. First API Request (Session Creation)**

#### **Client Request:**
```http
GET /api/retreats
Headers: 
  X-API-Key: retreat_api_key_2024
```

#### **Server Processing:**
```php
// ApiAuthentication Middleware (lines 45-57)
$sessionId = $request->header('X-Session-ID'); // NULL (first request)

if (!$sessionId) {
    // Generate unique session ID
    $sessionId = 'api_session_' . Str::uuid();
    // Example: "api_session_a1b2c3d4-e5f6-7g8h-9i0j-k1l2m3n4o5p6"
    
    // Store session data in cache
    Cache::put("api_session:{$sessionId}", [
        'created_at' => now(),
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'last_activity' => now()
    ], 60 * 24); // 24 hours
}
```

#### **Server Response:**
```http
HTTP/1.1 200 OK
X-Session-ID: api_session_a1b2c3d4-e5f6-7g8h-9i0j-k1l2m3n4o5p6
Content-Type: application/json

{
  "success": true,
  "message": "Available retreats retrieved successfully",
  "data": {
    "retreats": [...],
    "count": 5
  }
}
```

### **2. Subsequent Requests (Session Continuation)**

#### **Client Request:**
```http
GET /api/retreats/1
Headers: 
  X-API-Key: retreat_api_key_2024
  X-Session-ID: api_session_a1b2c3d4-e5f6-7g8h-9i0j-k1l2m3n4o5p6
```

#### **Server Processing:**
```php
// ApiAuthentication Middleware (lines 58-65)
$sessionId = $request->header('X-Session-ID'); // Existing session

// Update existing session
$sessionData = Cache::get("api_session:{$sessionId}");
if ($sessionData) {
    $sessionData['last_activity'] = now();
    Cache::put("api_session:{$sessionId}", $sessionData, 60 * 24); // Refresh expiry
}

// Add session ID to request for controllers to use
$request->merge(['session_id' => $sessionId]);
```

---

## 💾 **Session Data Structure**

### **Cache Storage:**
```php
// Cache Key Pattern:
"api_session:api_session_a1b2c3d4-e5f6-7g8h-9i0j-k1l2m3n4o5p6"

// Cache Value:
[
    'created_at' => Carbon('2025-01-26 19:59:18'),
    'ip_address' => '192.168.1.100',
    'user_agent' => 'MyBookingApp/1.0 (iOS 14.0)',
    'last_activity' => Carbon('2025-01-26 20:15:30')
]

// Expiration: 24 hours from last activity
```

### **Session ID Availability:**
```php
// Controllers can access session ID:
class BookingAPIController extends BaseAPIController
{
    public function store(Request $request)
    {
        $sessionId = $request->input('session_id');
        
        // Use session ID for logging, analytics, etc.
        Log::info("Booking created in session: {$sessionId}");
    }
}
```

---

## 📊 **Current Use Cases**

### **1. Request Tracking**
```php
// Every API request updates session last_activity
// Allows tracking of:
- Session duration
- API usage patterns
- Client activity levels
- Session abandonment rates
```

### **2. Client Identification**
```php
// Each session contains:
- IP Address: Geographic/network analysis
- User Agent: Platform/app identification  
- Creation Time: Session lifecycle tracking
- Last Activity: Inactivity detection
```

### **3. Analytics Data**
```php
// Sample analytics queries:
$activeSessions = Cache::many(['api_session:*']);
$mobileUsers = collect($activeSessions)->filter(function($session) {
    return str_contains($session['user_agent'], 'Mobile');
});

$sessionDurations = collect($activeSessions)->map(function($session) {
    return $session['last_activity']->diffInMinutes($session['created_at']);
});
```

---

## 🚀 **Future Enhancement Opportunities**

### **1. Enhanced State Storage**
```php
// Store booking flow state
Cache::put("session:{$sessionId}:booking_flow", [
    'step' => 'participants_added',
    'retreat_id' => 5,
    'temp_participants' => [
        ['name' => 'John Doe', 'email' => 'john@example.com'],
        ['name' => 'Jane Doe', 'email' => 'jane@example.com']
    ],
    'pricing_calculated' => true,
    'total_amount' => 1500.00,
    'step_completed_at' => now()
]);
```

### **2. Rate Limiting**
```php
// Per-session rate limiting
public function handle(Request $request, Closure $next): Response
{
    $sessionId = $request->header('X-Session-ID');
    $requestKey = "session:{$sessionId}:requests:" . now()->format('Y-m-d-H');
    $requestCount = Cache::increment($requestKey);
    
    if ($requestCount > 100) { // 100 requests per hour
        return response()->json([
            'success' => false,
            'message' => 'Rate limit exceeded for this session',
            'error_code' => 'RATE_LIMIT_EXCEEDED'
        ], 429);
    }
    
    return $next($request);
}
```

### **3. Security Enhancements**
```php
// IP address validation
public function handle(Request $request, Closure $next): Response
{
    $sessionId = $request->header('X-Session-ID');
    $sessionData = Cache::get("api_session:{$sessionId}");
    
    if ($sessionData && $sessionData['ip_address'] !== $request->ip()) {
        Log::warning("Session IP mismatch", [
            'session_id' => $sessionId,
            'original_ip' => $sessionData['ip_address'],
            'current_ip' => $request->ip()
        ]);
        
        // Option 1: Block request
        return response()->json(['error' => 'Session security violation'], 403);
        
        // Option 2: Update IP (for mobile users with changing IPs)
        $sessionData['ip_address'] = $request->ip();
        $sessionData['ip_changed_at'] = now();
        Cache::put("api_session:{$sessionId}", $sessionData, 60 * 24);
    }
    
    return $next($request);
}
```

### **4. Multi-Step Booking Process**
```php
// Step-based booking with session state
class BookingAPIController extends BaseAPIController
{
    // Step 1: Initialize booking
    public function initializeBooking(Request $request)
    {
        $sessionId = $request->input('session_id');
        
        Cache::put("session:{$sessionId}:booking", [
            'step' => 1,
            'retreat_id' => $request->retreat_id,
            'started_at' => now()
        ], 60 * 24);
        
        return $this->sendResponse(['step' => 1], 'Booking initialized');
    }
    
    // Step 2: Add participants
    public function addParticipants(Request $request)
    {
        $sessionId = $request->input('session_id');
        $bookingState = Cache::get("session:{$sessionId}:booking");
        
        if (!$bookingState || $bookingState['step'] !== 1) {
            return $this->sendError('Invalid booking state');
        }
        
        $bookingState['step'] = 2;
        $bookingState['participants'] = $request->participants;
        $bookingState['step_2_completed_at'] = now();
        
        Cache::put("session:{$sessionId}:booking", $bookingState, 60 * 24);
        
        return $this->sendResponse(['step' => 2], 'Participants added');
    }
    
    // Step 3: Confirm booking
    public function confirmBooking(Request $request)
    {
        $sessionId = $request->input('session_id');
        $bookingState = Cache::get("session:{$sessionId}:booking");
        
        if (!$bookingState || $bookingState['step'] !== 2) {
            return $this->sendError('Invalid booking state');
        }
        
        // Create actual booking
        $booking = Booking::create([...]);
        
        // Clear session booking state
        Cache::forget("session:{$sessionId}:booking");
        
        return $this->sendCreated(['booking_id' => $booking->booking_id]);
    }
}
```

### **5. Advanced Analytics**
```php
// Session analytics service
class SessionAnalyticsService
{
    public function getDailyStats($date)
    {
        // Get all sessions for a date
        $sessions = $this->getSessionsForDate($date);
        
        return [
            'total_sessions' => $sessions->count(),
            'unique_ips' => $sessions->pluck('ip_address')->unique()->count(),
            'mobile_sessions' => $sessions->filter(function($s) {
                return str_contains($s['user_agent'], 'Mobile');
            })->count(),
            'avg_duration_minutes' => $sessions->map(function($s) {
                return $s['last_activity']->diffInMinutes($s['created_at']);
            })->avg(),
            'api_calls_per_session' => $this->getApiCallsPerSession($sessions),
            'conversion_rate' => $this->getBookingConversionRate($sessions)
        ];
    }
    
    public function getPopularEndpoints($sessionId)
    {
        return Cache::get("session:{$sessionId}:endpoints", []);
    }
    
    public function trackEndpointUsage($sessionId, $endpoint)
    {
        $endpoints = Cache::get("session:{$sessionId}:endpoints", []);
        $endpoints[$endpoint] = ($endpoints[$endpoint] ?? 0) + 1;
        Cache::put("session:{$sessionId}:endpoints", $endpoints, 60 * 24);
    }
}
```

---

## 🔍 **Real-World Example Flow**

### **Complete Mobile App Booking Session:**

```http
# 1. App Launch - Get retreats list
GET /api/retreats
Headers: X-API-Key: retreat_api_key_2024
Response Headers: X-Session-ID: api_session_abc123

# 2. User browses specific retreat
GET /api/retreats/5
Headers: 
  X-API-Key: retreat_api_key_2024
  X-Session-ID: api_session_abc123

# 3. User fills booking form and submits
POST /api/bookings
Headers: 
  X-API-Key: retreat_api_key_2024
  X-Session-ID: api_session_abc123
Body: {retreat_id: 5, participants: [...]}
Response: {booking_id: "RB789"}

# 4. User checks booking confirmation
GET /api/bookings?booking_id=RB789&whatsapp_number=9876543210
Headers: 
  X-API-Key: retreat_api_key_2024
  X-Session-ID: api_session_abc123

# 5. Later, user cancels one participant
PATCH /api/bookings/RB789/cancel
Headers: 
  X-API-Key: retreat_api_key_2024
  X-Session-ID: api_session_abc123
Body: {serial_number: 2}
```

### **Backend Session Analysis:**
```php
Session ID: api_session_abc123
Duration: 45 minutes (10:00 AM - 10:45 AM)
Total API Calls: 5
Client: MyRetreatApp/2.1 (iPhone; iOS 14.0)
IP Address: 192.168.1.100
Journey: Browse → View Details → Book → Confirm → Cancel Participant
Result: Successful booking with partial cancellation
```

---

## 🛡️ **Security Considerations**

### **Current Security Features:**
- ✅ Session expiration (24 hours)
- ✅ IP address logging
- ✅ User agent tracking
- ✅ Cache-based storage (secure)
- ✅ UUID-based session IDs (hard to guess)

### **Security Best Practices:**
```php
// 1. Session ID Validation
if (!preg_match('/^api_session_[a-f0-9-]{36}$/', $sessionId)) {
    return response()->json(['error' => 'Invalid session ID format'], 400);
}

// 2. IP Address Monitoring
$previousIP = $sessionData['ip_address'];
$currentIP = $request->ip();

if ($previousIP !== $currentIP) {
    // Log for security monitoring
    Log::warning('Session IP changed', [
        'session_id' => $sessionId,
        'previous_ip' => $previousIP,
        'current_ip' => $currentIP,
        'user_agent' => $request->userAgent()
    ]);
}

// 3. Rate Limiting
$rateLimitKey = "rate_limit:session:{$sessionId}";
$requestCount = Cache::increment($rateLimitKey, 1);
Cache::expire($rateLimitKey, 3600); // 1 hour window

if ($requestCount > 200) { // 200 requests per hour
    return response()->json(['error' => 'Rate limit exceeded'], 429);
}
```

---

## 📈 **Performance Considerations**

### **Cache Optimization:**
```php
// Use Redis for better performance
// config/cache.php
'default' => 'redis',

// Or use dedicated cache store for sessions
'stores' => [
    'session_cache' => [
        'driver' => 'redis',
        'connection' => 'sessions',
        'prefix' => 'api_sessions',
    ]
]

// In middleware:
Cache::store('session_cache')->put($key, $value, $ttl);
```

### **Memory Management:**
```php
// Clean expired sessions periodically
// Create Artisan command: php artisan make:command CleanExpiredSessions

class CleanExpiredSessions extends Command
{
    public function handle()
    {
        $pattern = "api_session:*";
        $keys = Cache::getRedis()->keys($pattern);
        
        foreach ($keys as $key) {
            $sessionData = Cache::get($key);
            
            // Remove sessions inactive for more than 24 hours
            if ($sessionData && $sessionData['last_activity']->addHours(24)->isPast()) {
                Cache::forget($key);
                $this->info("Cleaned expired session: {$key}");
            }
        }
    }
}

// Schedule in Kernel.php:
$schedule->command('sessions:clean')->daily();
```

---

## 🔧 **Configuration Options**

### **Environment Variables:**
```bash
# .env additions for session management
SESSION_LIFETIME=1440  # 24 hours in minutes
SESSION_CLEANUP_ENABLED=true
SESSION_RATE_LIMIT=200  # requests per hour per session
SESSION_IP_VALIDATION=false  # disable for mobile apps
SESSION_ANALYTICS_ENABLED=true
```

### **Config File:**
```php
// config/api.php
return [
    'session' => [
        'lifetime' => env('SESSION_LIFETIME', 1440), // minutes
        'cleanup_enabled' => env('SESSION_CLEANUP_ENABLED', true),
        'rate_limit' => env('SESSION_RATE_LIMIT', 200),
        'ip_validation' => env('SESSION_IP_VALIDATION', false),
        'analytics_enabled' => env('SESSION_ANALYTICS_ENABLED', true),
        'cache_store' => 'redis', // or 'file'
        'prefix' => 'api_session:',
    ],
    
    'rate_limiting' => [
        'enabled' => env('API_RATE_LIMITING', true),
        'requests_per_hour' => env('API_REQUESTS_PER_HOUR', 200),
        'burst_limit' => env('API_BURST_LIMIT', 20),
    ]
];
```

---

## 📚 **API Client Integration Guide**

### **Mobile App Integration:**
```javascript
// React Native / JavaScript example
class RetreatAPIClient {
    constructor() {
        this.baseURL = 'https://your-domain.com/api';
        this.apiKey = 'retreat_api_key_2024';
        this.sessionId = null;
    }
    
    async request(endpoint, options = {}) {
        const headers = {
            'X-API-Key': this.apiKey,
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        // Add session ID if we have one
        if (this.sessionId) {
            headers['X-Session-ID'] = this.sessionId;
        }
        
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers
        });
        
        // Store session ID from response
        const newSessionId = response.headers.get('X-Session-ID');
        if (newSessionId) {
            this.sessionId = newSessionId;
            // Store in local storage for persistence
            localStorage.setItem('retreat_session_id', newSessionId);
        }
        
        return response.json();
    }
    
    // Load session from storage on app restart
    loadSession() {
        this.sessionId = localStorage.getItem('retreat_session_id');
    }
}

// Usage
const apiClient = new RetreatAPIClient();
apiClient.loadSession(); // Load saved session

// First request creates session
const retreats = await apiClient.request('/retreats');

// Subsequent requests use same session
const retreatDetails = await apiClient.request('/retreats/5');
const booking = await apiClient.request('/bookings', {
    method: 'POST',
    body: JSON.stringify({retreat_id: 5, participants: [...]})
});
```

### **PHP Client Integration:**
```php
class RetreatAPIClient 
{
    private $baseUrl = 'https://your-domain.com/api';
    private $apiKey = 'retreat_api_key_2024';
    private $sessionId = null;
    
    public function request($endpoint, $method = 'GET', $data = null)
    {
        $headers = [
            'X-API-Key: ' . $this->apiKey,
            'Content-Type: application/json'
        ];
        
        if ($this->sessionId) {
            $headers[] = 'X-Session-ID: ' . $this->sessionId;
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADERFUNCTION => [$this, 'handleHeader']
        ]);
        
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }
    
    private function handleHeader($curl, $header)
    {
        if (strpos($header, 'X-Session-ID:') === 0) {
            $this->sessionId = trim(substr($header, 13));
        }
        return strlen($header);
    }
}
```

---

## 🎯 **Summary**

The API State Management system provides:

✅ **Automatic session creation and tracking**  
✅ **24-hour session lifetime with activity-based renewal**  
✅ **Client identification through IP and User-Agent**  
✅ **Foundation for advanced features like rate limiting**  
✅ **Analytics and monitoring capabilities**  
✅ **Mobile-friendly session persistence**  
✅ **Security monitoring and fraud detection**  
✅ **Scalable Redis-based storage**  

This system transforms a stateless API into a stateful, trackable, and analyzable service while maintaining the simplicity and security of API key authentication.

---

**Last Updated:** January 26, 2025  
**Version:** 1.0  
**Maintainer:** Retreat Management System Team