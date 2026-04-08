# Multi-Tenant Retreat Booking System - Complete Documentation

## Overview
This system uses **path-based routing** to manage multiple organizations. Instead of subdomain-based routing (org1.retreatms.local), the system uses URL paths (/org1/, /org2/, /all/).

---

## URL Structure

### Frontend Routes
- `/all/` - Shows data from ALL organizations (no filtering)
- `/org1/` - Shows data specific to org1 organization
- `/org2/` - Shows data specific to org2 organization
- `/org1_church/` - Shows data specific to org1_church organization

### Frontend Pages
- `/all/` or `/org1/` - Home page with leaders and upcoming retreats
- `/all/retreats` or `/org1/retreats` - List of retreats for that organization
- `/all/register` or `/org1/register` - Registration form with organization-filtered retreats
- `/all/check-status` or `/org1/check-status` - Check booking status

### Admin Routes (NOT organization-scoped)
- `/admin/` - Admin dashboard
- `/admin/retreats` - Manage retreats
- `/admin/bookings` - Manage bookings
- `/admin/leaders` - Manage leaders
- `/admin/organizations` - Manage organizations

### Authentication Routes (NOT organization-scoped)
- `/login` - Admin login page
- `/logout` - Logout action

### Root URL Redirect
- `/` or empty path → Redirects to `/all/`

---

## How Organization Filtering Works

### 1. Organization Middleware (`app/Http/Middleware/IdentifyOrganization.php`)
- Extracts organization slug from URL path parameter
- If slug is `all` → No filtering (shows all organizations' data)
- If slug is `org1`, `org2`, etc. → Filters data for that specific organization
- Stores organization globally in config and view

### 2. Organization Helper (`app/Helpers/OrganizationHelper.php`)
- `currentId()` - Returns current organization ID (null if viewing /all/)
- `currentSlug()` - Returns current organization slug
- Used by controllers and models to apply filtering

### 3. Model Scopes
Models use `forOrganization($organizationId)` scope to filter data:
```php
// In Retreat model
public function scopeForOrganization($query, $organizationId)
{
    return $query->where('organization_id', $organizationId);
}
```

---

## Key Files & Their Roles

### Routing
- **`routes/web.php`** - Main route definitions
  - Admin routes at `/admin/` prefix (no organization parameter)
  - Auth routes at root level (no organization parameter)
  - Frontend routes at `/{organization}/` prefix (with organization parameter)

### Middleware
- **`app/Http/Middleware/IdentifyOrganization.php`** - Extracts and validates organization from URL
- **`bootstrap/app.php`** - Registers organization middleware

### Controllers
- **`app/Http/Controllers/Frontend/HomeController.php`** - Home page with leaders and retreats
- **`app/Http/Controllers/Frontend/RetreatController.php`** - Retreat listing and details
- **`app/Http/Controllers/Frontend/BookingController.php`** - Booking registration and status
- **`app/Http/Controllers/Api/RetreatAPIController.php`** - API for retreats (applies organization filtering)

### Views
- **`resources/views/frontend/home.blade.php`** - Home page
  - Shows 3 default leaders (always)
  - Shows organization-specific leaders (if any exist)
- **`resources/views/frontend/booking/register.blade.php`** - Registration form
  - Dropdown and table show only organization's retreats
- **`resources/views/frontend/layout.blade.php`** - Main layout with navbar
  - Organization selection dropdown
  - Login icon

### Helpers
- **`app/Helpers/helpers.php`** - Contains `org_route()` helper
  - Generates URLs with organization parameter
  - Example: `org_route('retreats.index')` → `/org1/retreats`

---

## Data Flow Examples

### Example 1: Viewing Home Page for org1_church
1. User visits `https://retreatms.local/org1_church/`
2. Route matches `/{organization}/` with organization = "org1_church"
3. IdentifyOrganization middleware:
   - Finds organization with slug "org1_church"
   - Stores it globally
4. HomeController:
   - Loads 3 default leaders (organization_id = null)
   - Loads org1_church's leaders (organization_id = org1_church's ID)
5. View displays:
   - First section: 3 default leaders
   - Second section: org1_church's team leaders

### Example 2: Viewing Retreats for /all/
1. User visits `https://retreatms.local/all/retreats`
2. Route matches `/{organization}/retreats` with organization = "all"
3. IdentifyOrganization middleware:
   - Recognizes "all" as special case
   - Sets no organization filtering
4. RetreatAPIController:
   - Queries all retreats (no organization filter)
5. View displays all organizations' retreats

### Example 3: Registering for org1_church
1. User visits `https://retreatms.local/org1_church/register`
2. BookingController.create():
   - Calls RetreatAPIController with organization context
   - API applies organization filtering
   - Returns only org1_church's retreats
3. Register view:
   - Dropdown shows only org1_church's retreats
   - Table shows only org1_church's retreats

---

## Leaders Display Logic

### Default Leaders (Always Shown)
- Stored in database with `organization_id = NULL`
- Always displayed in "Meet the Leadership" section
- Limited to 3 leaders

### Organization-Specific Leaders
- Stored in database with `organization_id = [specific org ID]`
- Displayed in "Our Team" section (only if they exist)
- Shown after default leaders with visual separator

### Display Rules
- `/all/` → Shows 3 default leaders + ALL organization leaders combined
- `/org1/` → Shows 3 default leaders + ONLY org1's leaders
- `/org2/` → Shows 3 default leaders + ONLY org2's leaders

---

## Retreats Display Logic

### Retreat Filtering
- Retreats have `organization_id` field
- API applies filtering based on current organization
- Only retreats with `available_spots > 0` shown on register page

### Register Page Consistency
- Register page uses same API as `/retreats` page
- Both show identical retreat lists for same organization
- Ensures consistency across the application

---

## URL Helper Function

### `org_route()` Helper
Located in `app/Helpers/helpers.php`

```php
org_route($routeName, $parameters = [])
```

**Usage:**
```php
// In views
<a href="{{ org_route('retreats.index') }}">View Retreats</a>
// Output: /org1/retreats (if viewing org1)

<a href="{{ org_route('booking.register') }}">Register</a>
// Output: /org1/register (if viewing org1)
```

**How it works:**
- Automatically includes current organization parameter
- Works with any route name
- Maintains organization context across navigation

---

## Database Schema

### Leaders Table
```
id, organization_id, name, title, description, image, display_order, is_active, created_at, updated_at
```
- `organization_id = NULL` → Default leader (shown on all pages)
- `organization_id = [ID]` → Organization-specific leader

### Retreats Table
```
id, organization_id, title, description, start_date, end_date, seats, criteria, is_featured, is_active, ...
```
- `organization_id` determines which organization owns the retreat

### Organizations Table
```
id, slug, name, is_active, is_verified, ...
```
- `slug` is used in URL path (e.g., "org1", "org1_church")

---

## Configuration

### Environment Variables
```
BASE_DOMAIN=retreatms.local
```

### Route Registration
In `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->group('organization', [
        IdentifyOrganization::class,
    ]);
})
```

---

## Common Tasks

### Add a New Organization
1. Create organization in admin panel with slug (e.g., "org3")
2. Add leaders with `organization_id = org3's ID`
3. Add retreats with `organization_id = org3's ID`
4. Access via `/org3/`

### Add Default Leaders
1. Create leader with `organization_id = NULL`
2. Set `is_active = true`
3. Set `display_order` for positioning
4. Appears on all organization pages

### Add Organization-Specific Leaders
1. Create leader with `organization_id = [specific org ID]`
2. Set `is_active = true`
3. Set `display_order` for positioning
4. Appears only on that organization's pages

### Generate Organization URLs
```php
// In controller
return redirect(org_route('home'));

// In view
<a href="{{ org_route('retreats.index') }}">Retreats</a>
<a href="{{ org_route('booking.register') }}">Register</a>
```

---

## Testing URLs

### Default Organization (/all/)
- Home: `https://retreatms.local/all/`
- Retreats: `https://retreatms.local/all/retreats`
- Register: `https://retreatms.local/all/register`
- Check Status: `https://retreatms.local/all/check-status`

### Specific Organization (/org1/)
- Home: `https://retreatms.local/org1/`
- Retreats: `https://retreatms.local/org1/retreats`
- Register: `https://retreatms.local/org1/register`
- Check Status: `https://retreatms.local/org1/check-status`

### Admin (No organization parameter)
- Dashboard: `https://retreatms.local/admin/`
- Login: `https://retreatms.local/login`

---

## Troubleshooting

### Issue: Organization not found (404)
- Check organization slug in URL matches database
- Verify organization `is_active = true` and `is_verified = true`
- Check IdentifyOrganization middleware is registered

### Issue: Retreats not showing on register page
- Verify retreats have correct `organization_id`
- Check retreats have `available_spots > 0`
- Verify retreats have `is_active = true`

### Issue: Leaders not showing
- For default leaders: Check `organization_id = NULL`
- For org-specific leaders: Check `organization_id` matches organization
- Verify `is_active = true` for all leaders
- Check `display_order` is set

### Issue: Routes not working
- Verify `routes/web.php` has organization prefix
- Check middleware is registered in `bootstrap/app.php`
- Verify IdentifyOrganization middleware exists
- Check route names match in views

---

## File Locations Summary

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Frontend/
│   │   │   ├── HomeController.php
│   │   │   ├── RetreatController.php
│   │   │   └── BookingController.php
│   │   └── Api/
│   │       └── RetreatAPIController.php
│   └── Middleware/
│       └── IdentifyOrganization.php
├── Helpers/
│   └── helpers.php
└── Models/
    ├── Leader.php
    ├── Retreat.php
    └── Organization.php

routes/
└── web.php

resources/views/frontend/
├── home.blade.php
├── layout.blade.php
└── booking/
    └── register.blade.php

bootstrap/
└── app.php
```

---

## Summary

This multi-tenant system uses path-based routing to serve multiple organizations from a single application. The organization parameter in the URL determines what data is displayed. Default leaders are always shown, while organization-specific leaders are shown only for that organization. All filtering is handled transparently through middleware and model scopes.
