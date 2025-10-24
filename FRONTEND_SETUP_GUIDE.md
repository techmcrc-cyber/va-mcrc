# Frontend Setup Guide for myretreatbooking.com

## Overview
This guide will help you set up the public-facing frontend for myretreatbooking.com using the same Laravel codebase as your admin panel.

## What Was Created

### Controllers (app/Http/Controllers/Frontend/)
- `HomeController.php` - Homepage with upcoming retreats
- `RetreatController.php` - Browse and view retreat details
- `BookingController.php` - Registration and status checking

### Views (resources/views/frontend/)
- `layout.blade.php` - Main layout with navigation
- `home.blade.php` - Homepage
- `retreats/index.blade.php` - List all retreats
- `retreats/show.blade.php` - Single retreat details
- `booking/register.blade.php` - Registration form
- `booking/participant-form.blade.php` - Participant form partial
- `booking/success.blade.php` - Success page after registration
- `booking/check-status.blade.php` - Check booking status form
- `booking/status.blade.php` - Display booking details

### Routes (routes/web.php)
- Domain-based routing for myretreatbooking.com and admin.myretreatbooking.com
- Fallback routes for local development

## Step-by-Step Setup

### 1. Update Apache Virtual Hosts

Both domains should point to the same Laravel folder:

**Edit `/etc/apache2/sites-available/myretreatbooking.com.conf`:**
```apache
<VirtualHost *:80>
    ServerName myretreatbooking.com
    ServerAlias www.myretreatbooking.com
    DocumentRoot /var/www/admin.myretreatbooking.com/public
    
    <Directory /var/www/admin.myretreatbooking.com/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.0-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    ErrorLog ${APACHE_LOG_DIR}/main-error.log
    CustomLog ${APACHE_LOG_DIR}/main-access.log combined
    
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>
```

**Edit `/etc/apache2/sites-available/myretreatbooking.com-le-ssl.conf`:**
```apache
<VirtualHost *:443>
    ServerName myretreatbooking.com
    ServerAlias www.myretreatbooking.com
    DocumentRoot /var/www/admin.myretreatbooking.com/public
    
    <Directory /var/www/admin.myretreatbooking.com/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php/php8.0-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/myretreatbooking.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/myretreatbooking.com/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf
    
    ErrorLog ${APACHE_LOG_DIR}/main-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/main-ssl-access.log combined
</VirtualHost>
```

### 2. Backup and Apply Changes

```bash
# Backup current myretreatbooking.com content
sudo mv /var/www/myretreatbooking.com /var/www/myretreatbooking.com.backup

# Test Apache configuration
sudo apache2ctl configtest

# If test passes, restart Apache
sudo systemctl restart apache2

# Check status
sudo systemctl status apache2
```

### 3. No Additional Configuration Needed

The frontend directly uses the `BookingAPIController` class, so:
- ✅ No API key configuration needed for frontend
- ✅ No HTTP calls between frontend and API
- ✅ Same validation and business logic
- ✅ Better performance (no network overhead)

External applications still use the API via `/api/bookings` with authentication.

### 4. Clear Laravel Cache

```bash
cd /var/www/admin.myretreatbooking.com
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 5. Test the Setup

Visit these URLs:
- `https://myretreatbooking.com/` - Should show homepage
- `https://myretreatbooking.com/retreats` - Should list retreats
- `https://myretreatbooking.com/register` - Should show registration form
- `https://myretreatbooking.com/check-status` - Should show status check form
- `https://admin.myretreatbooking.com/admin/dashboard` - Should show admin panel

## Features

### Public Frontend (myretreatbooking.com)
1. **Homepage** - Welcome page with upcoming retreats
2. **Browse Retreats** - View all available retreats with filtering
3. **Retreat Details** - Detailed information about each retreat
4. **Registration** - Multi-participant booking form (up to 4 people)
5. **Check Status** - View booking details using Booking ID and WhatsApp number

### How It Works
- Frontend **directly calls** the existing `BookingAPIController` methods (no HTTP overhead)
- Uses the exact same validation and business logic as the API
- Booking data is validated and stored via the API controller
- Email confirmations are sent automatically via queued jobs
- Users can check their booking status anytime
- External applications can still use the API via HTTP endpoints

## Customization

### Change Colors
Edit `resources/views/frontend/layout.blade.php`:
```css
:root {
    --primary-color: #2c5f7d;
    --secondary-color: #4a90a4;
    --accent-color: #f39c12;
}
```

### Add Logo
Replace the text logo in `layout.blade.php`:
```html
<a class="navbar-brand" href="{{ route('home') }}">
    <img src="/images/logo.png" alt="Logo" height="40">
</a>
```

### Modify Email Templates
Email templates are in `resources/views/emails/`

## Troubleshooting

### Issue: 404 on myretreatbooking.com
**Solution:** Check that both virtual hosts point to the same DocumentRoot

### Issue: Routes not working
**Solution:** Clear route cache: `php artisan route:clear`

### Issue: API calls failing
**Solution:** Check API middleware in `routes/api.php` and ensure API key is configured

### Issue: Domain routing not working
**Solution:** Laravel domain routing requires proper server configuration. Check that `$_SERVER['HTTP_HOST']` is set correctly.

## Security Notes

1. **API Authentication** - The API uses `api.auth` middleware. Configure this properly.
2. **CORS** - If needed, configure CORS in `config/cors.php`
3. **Rate Limiting** - Consider adding rate limiting to prevent abuse
4. **Input Validation** - All inputs are validated before processing

## Support

For issues or questions:
- Check Laravel logs: `/var/www/admin.myretreatbooking.com/storage/logs/laravel.log`
- Check Apache logs: `/var/log/apache2/`
- Email: support@myretreatbooking.com

## Next Steps

1. Test all functionality thoroughly
2. Customize colors and branding
3. Add your logo and images
4. Configure email settings
5. Set up monitoring and backups
6. Add Google Analytics (optional)
7. Test on mobile devices

---

**Created:** October 24, 2024
**Version:** 1.0
