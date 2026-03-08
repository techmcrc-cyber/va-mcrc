# Multi-Tenant Implementation Guide

## Overview
This document outlines the multi-tenant system implementation for the retreat booking application, allowing multiple organizations to operate under their own subdomains.

## Architecture

### Subdomain Structure
- **Main Domain**: `myretreatbooking.com` - Super admin panel and landing page
- **Organization Subdomains**: `{org-slug}.myretreatbooking.com` - Each organization's frontend and admin
- **Example**: `mountcarmel.myretreatbooking.com`, `stfrancis.myretreatbooking.com`

## Database Changes

### New Tables

#### 1. `organizations`
Stores organization details and settings.

**Columns:**
- `id` - Primary key
- `name` - Organization name
- `slug` - Unique subdomain identifier
- `email`, `phone`, `address` - Contact information
- `logo` - Organization logo path
- `description` - About the organization
- `is_verified` - Verification status
- `is_active` - Active/inactive status
- `settings` - JSON field for org-specific settings
- `created_by`, `updated_by` - Audit fields
- `timestamps`, `soft_deletes`

#### 2. `leaders`
Stores leadership team members for each organization.

**Columns:**
- `id` - Primary key
- `organization_id` - Foreign key to organizations
- `name` - Leader name
- `title` - Position (Spiritual Director, Preacher, etc.)
- `description` - Bio/description
- `image` - Photo path
- `display_order` - Sort order
- `is_active` - Active/inactive status
- `created_by`, `updated_by` - Audit fields
- `timestamps`, `soft_deletes`

### Modified Tables

#### 1. `users`
- Added `organization_id` - Links user to organization

#### 2. `retreats`
- Added `organization_id` - Links retreat to organization

#### 3. `bookings`
- Added `organization_id` - Links booking to organization

## Models

### New Models
1. **Organization** (`app/Models/Organization.php`)
   - Relationships: users, retreats, bookings, leaders
   - Scopes: active(), verified()
   - Helper: getSubdomainUrl()

2. **Leader** (`app/Models/Leader.php`)
   - Relationships: organization, creator, updater
   - Scopes: active(), ordered(), forOrganization()

### Updated Models
1. **User** - Added organization() relationship
2. **Retreat** - Added organization() relationship and forOrganization() scope
3. **Booking** - Added organization() relationship and forOrganization() scope

## Middleware

### IdentifyOrganization
**Location**: `app/Http/Middleware/IdentifyOrganization.php`

**Purpose**: Extracts organization from subdomain and makes it available throughout the request.

**How it works:**
1. Extracts `{organization}` slug from route parameter
2. Finds organization in database
3. Stores in request, config, and shares with views
4. Returns 404 if organization not found or inactive

**Usage**: Applied to subdomain routes via `middleware('organization')`

## Routes

### Multi-Tenant Routes (`routes/web.php`)

**1. Organization Subdomain Routes**
```php
Route::domain('{organization}.myretreatbooking.com')
    ->middleware('organization')
    ->group(function () {
        // Frontend routes
        // Admin routes (organization-scoped)
    });
```

**2. Main Domain Routes**
```php
Route::domain('myretreatbooking.com')->group(function () {
    // Super admin panel
    // Landing page
});
```

**3. Local Development Fallback**
- Catches localhost and other domains for development

### Admin Routes (`routes/admin.php`)

**New Routes:**
- Organizations CRUD: `/admin/organizations/*` (Super Admin only)
- Leaders CRUD: `/admin/leaders/*` (Organization scoped)

## Controllers

### OrganizationController
**Location**: `app/Http/Controllers/Admin/OrganizationController.php`

**Features:**
- CRUD operations for organizations
- Creates organization owner user on organization creation
- Logo upload handling
- Super admin only access

**Key Methods:**
- `store()` - Creates organization + owner user in transaction
- `update()` - Updates organization details
- `destroy()` - Soft deletes organization

### LeaderController
**Location**: `app/Http/Controllers/Admin/LeaderController.php`

**Features:**
- CRUD operations for leaders
- Organization-scoped access
- Image upload handling
- Super admin can manage all, org admins only their own

**Key Methods:**
- `getCurrentOrganizationId()` - Gets org ID based on user role
- Access control checks in all methods

### Updated Controllers

**RetreatAPIController**
- Added organization scoping to `index()` and `show()` methods
- Uses `OrganizationHelper::currentId()` to get context

## Helpers

### OrganizationHelper
**Location**: `app/Helpers/OrganizationHelper.php`

**Methods:**
- `current()` - Get current organization object
- `currentId()` - Get current organization ID
- `hasContext()` - Check if in organization context
- `getUserOrganizationId()` - Get org ID for current user

## Permissions

### New Permissions
**Organizations:**
- `organizations.view`
- `organizations.create`
- `organizations.edit`
- `organizations.delete`

**Leaders:**
- `leaders.view`
- `leaders.create`
- `leaders.edit`
- `leaders.delete`

## Workflow

### 1. Organization Registration (Super Admin)
1. Super admin goes to `/admin/organizations/create`
2. Fills organization details + owner user details
3. System creates:
   - Organization record (verified & active)
   - Owner user account with "Organization Admin" role
4. Organization is now accessible at `{slug}.myretreatbooking.com`

### 2. Organization User Login
1. User visits `{org-slug}.myretreatbooking.com/login`
2. Logs in with credentials
3. Middleware identifies organization from subdomain
4. All queries automatically scoped to their organization

### 3. Data Isolation
- **Organization Users**: Can ONLY see/edit their own organization's data
- **Super Admin**: Can see ALL data across all organizations
- **Automatic Scoping**: Controllers use `forOrganization()` scope

### 4. Leader Management
1. Organization admin goes to `/admin/leaders`
2. Can add/edit/delete leaders for their organization
3. Leaders appear on organization's public website

## Migration Steps

### To Apply Changes:

```bash
# Run migrations
php artisan migrate

# The migrations will:
# 1. Create organizations table
# 2. Create leaders table
# 3. Add organization_id to users, retreats, bookings
# 4. Add new permissions
```

### Post-Migration Tasks:

1. **Create First Organization** (via Super Admin panel)
2. **Assign Existing Data** (if migrating from single-tenant):
   ```sql
   -- Create default organization
   INSERT INTO organizations (name, slug, is_verified, is_active, created_at, updated_at)
   VALUES ('Mount Carmel', 'mountcarmel', 1, 1, NOW(), NOW());
   
   -- Assign existing retreats
   UPDATE retreats SET organization_id = 1;
   
   -- Assign existing bookings
   UPDATE bookings SET organization_id = 1;
   
   -- Assign existing users
   UPDATE users SET organization_id = 1 WHERE role_id != 1; -- Exclude super admin
   ```

## Testing

### Local Development
1. Add to `/etc/hosts` (Linux/Mac) or `C:\Windows\System32\drivers\etc\hosts` (Windows):
   ```
   127.0.0.1 myretreatbooking.local
   127.0.0.1 mountcarmel.myretreatbooking.local
   127.0.0.1 stfrancis.myretreatbooking.local
   ```

2. Update `.env`:
   ```
   APP_URL=http://myretreatbooking.local
   SESSION_DOMAIN=.myretreatbooking.local
   ```

3. Configure web server (Apache/Nginx) to handle wildcard subdomains

### Test Scenarios
1. ✅ Create organization via super admin
2. ✅ Login as organization owner
3. ✅ Create retreat (should be scoped to organization)
4. ✅ View retreats on subdomain (should only show org's retreats)
5. ✅ Create booking (should be scoped to organization)
6. ✅ Add leaders (should be scoped to organization)
7. ✅ Verify data isolation between organizations

## Next Steps

### Views (To Be Created)
1. `resources/views/admin/organizations/` - Organization CRUD views
2. `resources/views/admin/leaders/` - Leader CRUD views
3. Update frontend views to display leaders

### Additional Features
1. Organization branding (colors, logo on frontend)
2. Organization-specific email templates
3. Organization settings page
4. Multi-organization user support (if needed)
5. Organization analytics dashboard

## Security Considerations

1. **Data Isolation**: All queries must be scoped by organization_id
2. **Access Control**: Middleware checks organization ownership
3. **Super Admin**: Has unrestricted access (use carefully)
4. **Subdomain Validation**: Middleware validates organization exists and is active
5. **File Uploads**: Organize by organization (e.g., `storage/organizations/{id}/`)

## File Structure

```
app/
├── Models/
│   ├── Organization.php (new)
│   ├── Leader.php (new)
│   ├── User.php (updated)
│   ├── Retreat.php (updated)
│   └── Booking.php (updated)
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── OrganizationController.php (new)
│   │       └── LeaderController.php (new)
│   └── Middleware/
│       └── IdentifyOrganization.php (new)
└── Helpers/
    └── OrganizationHelper.php (new)

database/migrations/
├── 2026_03_08_000001_create_organizations_table.php
├── 2026_03_08_000002_create_leaders_table.php
├── 2026_03_08_000003_add_organization_id_to_users_table.php
├── 2026_03_08_000004_add_organization_id_to_retreats_table.php
├── 2026_03_08_000005_add_organization_id_to_bookings_table.php
└── 2026_03_08_000006_add_organization_and_leader_permissions.php

routes/
├── web.php (updated with subdomain routing)
└── admin.php (updated with org & leader routes)
```

## Support

For issues or questions about the multi-tenant implementation, refer to:
- Laravel Multi-Tenancy Documentation
- This implementation guide
- Code comments in controllers and models
