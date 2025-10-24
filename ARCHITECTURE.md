# Architecture Overview

## Current Setup

```
┌─────────────────────────────────────────────────────────────┐
│                    SAME LARAVEL APPLICATION                  │
│         /var/www/admin.myretreatbooking.com/                │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────┐      ┌──────────────────────┐    │
│  │  PUBLIC FRONTEND     │      │   ADMIN PANEL        │    │
│  │  myretreatbooking    │      │   admin.myretreat    │    │
│  │  .com                │      │   booking.com        │    │
│  └──────────┬───────────┘      └──────────┬───────────┘    │
│             │                               │                │
│             │                               │                │
│             ▼                               ▼                │
│  ┌─────────────────────────────────────────────────────┐   │
│  │         SHARED CONTROLLERS & MODELS                  │   │
│  │                                                       │   │
│  │  Frontend Controllers  ──►  BookingAPIController     │   │
│  │  (Direct Method Calls)      (Business Logic)         │   │
│  │                                                       │   │
│  │  Admin Controllers     ──►  Models (Booking,         │   │
│  │  (CRUD Operations)          Retreat, User)           │   │
│  └─────────────────────────────────────────────────────┘   │
│                             │                                │
│                             ▼                                │
│                    ┌─────────────────┐                      │
│                    │    DATABASE     │                      │
│                    │    (MySQL)      │                      │
│                    └─────────────────┘                      │
│                                                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│              EXTERNAL API ACCESS (HTTP)                      │
│                                                               │
│  Mobile Apps / Third-party  ──►  /api/bookings              │
│  (with API Key Auth)             (BookingAPIController)      │
└─────────────────────────────────────────────────────────────┘
```

## How It Works

### 1. Public Frontend (myretreatbooking.com)
- **Users visit:** `https://myretreatbooking.com/`
- **Routes:** `routes/web.php` (domain-based routing)
- **Controllers:** `app/Http/Controllers/Frontend/`
- **Views:** `resources/views/frontend/`
- **Purpose:** Public-facing registration and status checking

**Flow:**
```
User fills form → Frontend\BookingController 
                → BookingAPIController->store() (direct call)
                → Validation & Business Logic
                → Save to Database
                → Send Email (queued)
                → Return success
```

### 2. Admin Panel (admin.myretreatbooking.com)
- **Admins visit:** `https://admin.myretreatbooking.com/admin/`
- **Routes:** `routes/admin.php`
- **Controllers:** `app/Http/Controllers/Admin/`
- **Views:** `resources/views/admin/`
- **Purpose:** Manage bookings, retreats, users, settings

**Flow:**
```
Admin logs in → Admin\BookingController
              → Direct database operations
              → CRUD operations on bookings
              → Manage retreat details
```

### 3. External API (for mobile apps, etc.)
- **Apps call:** `https://admin.myretreatbooking.com/api/bookings`
- **Routes:** `routes/api.php`
- **Controllers:** `app/Http/Controllers/API/`
- **Auth:** API Key middleware
- **Purpose:** Allow external applications to create/check bookings

**Flow:**
```
Mobile App → POST /api/bookings (with API key)
           → API Middleware (auth check)
           → BookingAPIController->store()
           → Same validation & logic as frontend
           → Return JSON response
```

## Key Benefits

### ✅ Single Codebase
- One Laravel application serves everything
- Shared models, validation, and business logic
- Easy to maintain and update

### ✅ No Duplication
- Frontend uses the **same** BookingAPIController as external APIs
- Same validation rules
- Same email templates
- Same database operations

### ✅ Performance
- Frontend → API: **Direct method calls** (no HTTP overhead)
- External → API: **HTTP calls** (with authentication)

### ✅ Security
- Admin panel: Protected by authentication
- Public frontend: Open access (as intended)
- External API: Protected by API key middleware

## Domain Routing

Laravel automatically routes requests based on domain:

```php
// routes/web.php

// Public routes
Route::domain('myretreatbooking.com')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/register', [BookingController::class, 'create']);
    // ... more public routes
});

// Admin routes
Route::domain('admin.myretreatbooking.com')->prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});
```

## File Structure

```
/var/www/admin.myretreatbooking.com/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   ├── API/            # API controllers (shared)
│   │   │   └── Frontend/       # Public frontend controllers
│   │   └── Middleware/
│   └── Models/                 # Shared models
├── routes/
│   ├── web.php                 # Frontend + Admin routing
│   ├── api.php                 # External API routes
│   └── admin.php               # Admin-specific routes
├── resources/
│   └── views/
│       ├── admin/              # Admin panel views
│       └── frontend/           # Public frontend views
└── public/                     # Web root (both domains point here)
```

## Apache Configuration

Both domains point to the **same** public folder:

```apache
# myretreatbooking.com
DocumentRoot /var/www/admin.myretreatbooking.com/public

# admin.myretreatbooking.com
DocumentRoot /var/www/admin.myretreatbooking.com/public
```

Laravel's routing handles the rest based on the domain name.

## Summary

- **One codebase** serves both domains
- **Frontend directly calls** API controller methods (no HTTP)
- **External apps** use HTTP API endpoints (with auth)
- **Admin panel** has separate authentication and routes
- **Efficient and maintainable** architecture
