# Local Testing Guide (Laragon/Windows)

## Quick Start - Testing Locally

Since you're using **Laragon on Windows** (`E:\laragon\www\retreatms`), you can test the frontend immediately without configuring domains.

### 1. Access the Frontend Locally

Open your browser and visit:

```
http://localhost/retreatms/
```

Or if Laragon auto-configured a domain:

```
http://retreatms.test/
```

### Available URLs (Local Testing)

| Page | URL |
|------|-----|
| **Homepage** | `http://localhost/retreatms/` |
| **Browse Retreats** | `http://localhost/retreatms/retreats` |
| **Register** | `http://localhost/retreatms/register` |
| **Check Status** | `http://localhost/retreatms/check-status` |
| **Admin Panel** | `http://localhost/retreatms/admin/dashboard` |

### 2. How Local Routing Works

The `routes/web.php` file now detects if you're on localhost and uses simple routes instead of domain-based routing:

```php
// Automatically detects localhost and uses these routes
if (!in_array(request()->getHost(), ['myretreatbooking.com', ...])) {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/retreats', [RetreatController::class, 'index']);
    // ... etc
}
```

### 3. Clear Laravel Cache

After updating routes, clear the cache:

```bash
cd E:\laragon\www\retreatms
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

Or use the shortcut:

```bash
php artisan optimize:clear
```

### 4. Test the Frontend

1. **Visit Homepage:**
   ```
   http://localhost/retreatms/
   ```
   You should see the welcome page with upcoming retreats.

2. **Browse Retreats:**
   ```
   http://localhost/retreatms/retreats
   ```
   Lists all active retreats.

3. **Register for a Retreat:**
   ```
   http://localhost/retreatms/register
   ```
   Fill out the registration form.

4. **Check Booking Status:**
   ```
   http://localhost/retreatms/check-status
   ```
   Enter Booking ID and WhatsApp number.

### 5. Test the Admin Panel

```
http://localhost/retreatms/admin/dashboard
```

(You'll need to login with your admin credentials)

## Troubleshooting

### Issue: "Target class [HomeController] does not exist"

**Solution:** Make sure the controllers exist:
```bash
php artisan route:list | grep Home
```

### Issue: "View [frontend.home] not found"

**Solution:** Check that views exist:
```bash
dir resources\views\frontend\
```

### Issue: Routes not working

**Solution:** Clear route cache:
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: 404 on all pages

**Solution:** Check your `.htaccess` or web server configuration. For Laragon, make sure mod_rewrite is enabled.

## Testing the API Integration

### Test Registration Flow

1. Go to: `http://localhost/retreatms/register`
2. Select a retreat
3. Fill in participant details
4. Submit the form
5. You should see a success page with Booking ID

### Test Status Check Flow

1. Go to: `http://localhost/retreatms/check-status`
2. Enter the Booking ID from registration
3. Enter the WhatsApp number you used
4. Submit
5. You should see full booking details

## Database Setup

Make sure your database is configured in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations if needed:
```bash
php artisan migrate
```

## Email Testing (Optional)

For local testing, use Mailtrap or log emails to file:

```env
MAIL_MAILER=log
```

This will save emails to `storage/logs/laravel.log` instead of sending them.

## Production Deployment

When you're ready to deploy to production:

1. Upload code to `/var/www/admin.myretreatbooking.com/`
2. Configure Apache virtual hosts (see FRONTEND_SETUP_GUIDE.md)
3. The domain-based routing will automatically work
4. Local fallback routes will be ignored

## Quick Commands Reference

```bash
# Clear all caches
php artisan optimize:clear

# View all routes
php artisan route:list

# Check for errors
php artisan route:list | grep frontend

# Start development server (alternative to Laragon)
php artisan serve
# Then visit: http://127.0.0.1:8000
```

## Laragon-Specific Tips

### Auto-Domain Feature

Laragon can auto-create domains like `retreatms.test`. To use this:

1. Right-click Laragon tray icon
2. Go to "Preferences" → "General"
3. Check "Auto create virtual hosts"
4. Your site will be available at: `http://retreatms.test`

### Pretty URLs

Laragon automatically handles pretty URLs, so you don't need to worry about `.htaccess` configuration.

### Database Access

- **phpMyAdmin:** `http://localhost/phpmyadmin`
- **Adminer:** `http://localhost/adminer` (if installed)

## Next Steps

1. ✅ Test locally using `http://localhost/retreatms/`
2. ✅ Create some test retreats in admin panel
3. ✅ Test registration flow
4. ✅ Test status check flow
5. ✅ Verify emails are being logged/sent
6. 🚀 Deploy to production when ready

---

**Note:** The domain-based routing (`myretreatbooking.com` vs `admin.myretreatbooking.com`) only works in production. For local testing, use the URLs above.
