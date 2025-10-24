# Quick Start Guide

## 🚀 Get Started in 3 Steps

### Step 1: Clear Cache
```bash
cd E:\laragon\www\retreatms
php artisan optimize:clear
```

### Step 2: Create Admin User (if needed)
```bash
php artisan tinker
```
Then paste:
```php
App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@myretreatbooking.com',
    'password' => bcrypt('password123'),
    'is_active' => true
]);
exit
```

### Step 3: Login and Test
1. **Login:** `http://localhost/retreatms/login`
2. **Email:** `admin@myretreatbooking.com`
3. **Password:** `password123`

---

## 📍 All URLs (Local Testing)

| Page | URL |
|------|-----|
| **Frontend Homepage** | `http://localhost/retreatms/` |
| **Browse Retreats** | `http://localhost/retreatms/retreats` |
| **Register** | `http://localhost/retreatms/register` |
| **Check Status** | `http://localhost/retreatms/check-status` |
| **Admin Login** | `http://localhost/retreatms/login` |
| **Admin Dashboard** | `http://localhost/retreatms/admin/dashboard` |

---

## 🎯 Test the Complete Flow

### 1. Create a Retreat (Admin)
1. Login to admin: `http://localhost/retreatms/login`
2. Go to Retreats: `http://localhost/retreatms/admin/retreats`
3. Click "Create New Retreat"
4. Fill in details and save

### 2. Register for Retreat (Public)
1. Visit: `http://localhost/retreatms/register`
2. Select the retreat you created
3. Fill in participant details
4. Submit
5. Note the Booking ID shown on success page

### 3. Check Booking Status (Public)
1. Visit: `http://localhost/retreatms/check-status`
2. Enter Booking ID and WhatsApp number
3. View booking details

### 4. Manage Booking (Admin)
1. Login to admin
2. Go to: `http://localhost/retreatms/admin/bookings`
3. View/Edit/Cancel bookings

---

## 📁 What Was Created

### Controllers
- ✅ `Frontend/HomeController.php` - Homepage
- ✅ `Frontend/RetreatController.php` - Browse retreats
- ✅ `Frontend/BookingController.php` - Registration & status
- ✅ Uses existing `API/BookingAPIController.php` for logic

### Views
- ✅ `frontend/home.blade.php` - Homepage
- ✅ `frontend/retreats/index.blade.php` - List retreats
- ✅ `frontend/retreats/show.blade.php` - Retreat details
- ✅ `frontend/booking/register.blade.php` - Registration form
- ✅ `frontend/booking/success.blade.php` - Success page
- ✅ `frontend/booking/check-status.blade.php` - Status form
- ✅ `frontend/booking/status.blade.php` - Booking details

### Routes
- ✅ `routes/web.php` - Frontend + Admin routing
- ✅ Domain-based routing for production
- ✅ Fallback routes for local testing
- ✅ Login routes added

---

## 🔧 Troubleshooting

### Routes not working?
```bash
php artisan route:clear
php artisan config:clear
```

### Can't login?
```bash
# Check if user exists
php artisan tinker
>>> App\Models\User::where('email', 'admin@myretreatbooking.com')->first();
>>> exit
```

### Views not found?
```bash
php artisan view:clear
```

### Database issues?
Check `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📚 Documentation Files

- **HOW_TO_LOGIN.md** - Detailed login instructions
- **LOCAL_TESTING_GUIDE.md** - Complete local testing guide
- **FRONTEND_SETUP_GUIDE.md** - Production deployment guide
- **ARCHITECTURE.md** - System architecture overview

---

## 🚀 Production Deployment

When ready to deploy:

1. Upload code to server
2. Update Apache virtual hosts (see FRONTEND_SETUP_GUIDE.md)
3. Run migrations: `php artisan migrate`
4. Clear cache: `php artisan optimize:clear`
5. Set permissions: `chmod -R 775 storage bootstrap/cache`

**Production URLs:**
- Frontend: `https://myretreatbooking.com/`
- Admin: `https://admin.myretreatbooking.com/login`

---

## ✅ Checklist

- [ ] Clear Laravel cache
- [ ] Create admin user
- [ ] Login to admin panel
- [ ] Create a test retreat
- [ ] Test registration from frontend
- [ ] Test status check
- [ ] Verify email is sent/logged
- [ ] Check booking in admin panel
- [ ] Ready for production!

---

**Need Help?** Check the detailed guides in the documentation files above.
