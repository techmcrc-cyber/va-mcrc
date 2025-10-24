# API Integration Summary

## ✅ All Frontend Routes Now Use APIs

All frontend controllers now **directly call the existing API controllers** instead of accessing the database directly.

### Controllers Updated

| Controller | API Used | Methods |
|------------|----------|---------|
| **HomeController** | `RetreatAPIController` | `index()` - Get upcoming retreats |
| **RetreatController** | `RetreatAPIController` | `index()` - List all retreats<br>`show()` - Get retreat details |
| **BookingController** | `BookingAPIController` | `store()` - Create booking<br>`show()` - Check booking status |

### How It Works

```
Frontend Request
    ↓
Frontend Controller (e.g., RetreatController)
    ↓
API Controller (e.g., RetreatAPIController) - Direct method call
    ↓
Business Logic & Validation
    ↓
Database
    ↓
JSON Response
    ↓
Frontend Controller processes response
    ↓
Blade View renders data
```

### Benefits

✅ **Single Source of Truth** - All logic in API controllers  
✅ **Consistent Validation** - Same rules for frontend and external apps  
✅ **No Code Duplication** - Reuse existing API logic  
✅ **Better Performance** - Direct method calls (no HTTP overhead)  
✅ **Easy Maintenance** - Update logic in one place  

## API Endpoints Used

### 1. Retreat API (`RetreatAPIController`)

**List Retreats:**
```php
$response = $retreatAPI->index($request);
// Returns: Available retreats with seat availability
```

**Get Retreat Details:**
```php
$response = $retreatAPI->show($request, $id);
// Returns: Full retreat details including availability, criteria, etc.
```

### 2. Booking API (`BookingAPIController`)

**Create Booking:**
```php
$response = $bookingAPI->store($request);
// Validates participants, checks availability, creates booking, sends email
```

**Check Booking Status:**
```php
$response = $bookingAPI->show($request);
// Returns: Full booking details with retreat info and participants
```

## Data Flow Example

### Homepage (List Retreats)

```php
// HomeController.php
public function index(Request $request)
{
    // Call API
    $response = $this->retreatAPI->index($request);
    $responseData = json_decode($response->getContent(), true);
    
    // Extract data
    $upcomingRetreats = collect($responseData['data']['retreats'])->take(6);
    
    // Pass to view
    return view('frontend.home', compact('upcomingRetreats'));
}
```

**API Response Format:**
```json
{
    "success": true,
    "data": {
        "retreats": [
            {
                "retreat_id": 1,
                "retreat_name": "Silent Retreat",
                "start_date": "2024-11-01",
                "end_date": "2024-11-03",
                "available_spots": 15,
                "total_seats": 20,
                "criteria": "open_to_all",
                "criteria_label": "Open to All"
            }
        ],
        "count": 5
    }
}
```

### Registration (Create Booking)

```php
// BookingController.php
public function store(Request $request)
{
    // Call API
    $response = $this->bookingAPI->store($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->isSuccessful()) {
        return redirect()->route('booking.success')
            ->with('booking_data', $responseData['data']);
    }
    
    // Handle errors
    return back()->withErrors($responseData['errors']);
}
```

**API Response Format (Success):**
```json
{
    "success": true,
    "data": {
        "booking_id": "BK20241024001",
        "retreat": {
            "id": 1,
            "name": "Silent Retreat",
            "start_date": "2024-11-01",
            "end_date": "2024-11-03"
        },
        "participants": [
            {
                "serial_number": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "role": "primary"
            }
        ],
        "total_participants": 1
    }
}
```

## Views Updated

All views now work with **array data from API** instead of Eloquent models:

### Before (Eloquent Model):
```blade
{{ $retreat->title }}
{{ $retreat->start_date->format('M d, Y') }}
{{ $retreat->seats }}
```

### After (API Array):
```blade
{{ $retreat['retreat_name'] }}
{{ \Carbon\Carbon::parse($retreat['start_date'])->format('M d, Y') }}
{{ $retreat['availability']['total_seats'] }}
```

## External API Access

External applications can still use the HTTP API endpoints:

```bash
# List retreats
GET https://admin.myretreatbooking.com/api/retreats
Headers: X-API-Key: your-api-key

# Get retreat details
GET https://admin.myretreatbooking.com/api/retreats/1
Headers: X-API-Key: your-api-key

# Create booking
POST https://admin.myretreatbooking.com/api/bookings
Headers: X-API-Key: your-api-key
Body: { retreat_id, participants: [...] }

# Check booking status
GET https://admin.myretreatbooking.com/api/bookings
Headers: 
  X-API-Key: your-api-key
  booking-id: BK20241024001
  whatsapp-number: 1234567890
```

## Testing

### Test Frontend (Uses API Internally)
```
http://localhost/retreatms/
http://localhost/retreatms/retreats
http://localhost/retreatms/register
http://localhost/retreatms/check-status
```

### Test External API (HTTP)
```bash
# Using curl
curl -X GET "http://localhost/retreatms/api/retreats" \
  -H "Accept: application/json" \
  -H "X-API-Key: your-api-key"
```

## Summary

✅ **All frontend routes now use the existing APIs**  
✅ **No direct database access from frontend controllers**  
✅ **Consistent business logic across all entry points**  
✅ **Views updated to work with API response format**  
✅ **External apps can still use HTTP API endpoints**  

The frontend is now a **consumer of your APIs**, just like any external application would be, but with the performance benefit of direct method calls instead of HTTP requests.
