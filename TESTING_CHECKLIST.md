# Testing Checklist

## ✅ Fixed Issues

- [x] All frontend controllers now use API controllers
- [x] Views updated to work with API array format
- [x] Fixed `$retreat->title` error in show view
- [x] Login routes added

## 🧪 Test These URLs

### 1. Clear Cache First
```bash
cd E:\laragon\www\retreatms
php artisan optimize:clear
```

### 2. Test Frontend Pages

| Page | URL | Expected Result |
|------|-----|-----------------|
| **Homepage** | `http://localhost/retreatms/` | Shows upcoming retreats |
| **All Retreats** | `http://localhost/retreatms/retreats` | Lists all available retreats |
| **Retreat Details** | `http://localhost/retreatms/retreats/1` | Shows retreat details (replace 1 with actual ID) |
| **Registration** | `http://localhost/retreatms/register` | Shows registration form |
| **Check Status** | `http://localhost/retreatms/check-status` | Shows status check form |

### 3. Test Admin Panel

| Page | URL | Expected Result |
|------|-----|-----------------|
| **Login** | `http://localhost/retreatms/login` | Shows login form |
| **Dashboard** | `http://localhost/retreatms/admin/dashboard` | Shows admin dashboard (after login) |
| **Retreats** | `http://localhost/retreatms/admin/retreats` | Manage retreats |
| **Bookings** | `http://localhost/retreatms/admin/bookings` | Manage bookings |

## 🔍 What to Check

### Homepage (`/`)
- [ ] Page loads without errors
- [ ] Shows up to 6 upcoming retreats
- [ ] Each retreat card shows:
  - Retreat name
  - Start and end dates
  - Available seats
  - "View Details" button

### Retreats List (`/retreats`)
- [ ] Page loads without errors
- [ ] Shows all available retreats
- [ ] Each retreat shows availability progress bar
- [ ] Clicking "View Details" goes to retreat page

### Retreat Details (`/retreats/1`)
- [ ] Page loads without errors (NO "Attempt to read property title on array" error)
- [ ] Shows retreat name, description, dates
- [ ] Shows availability
- [ ] Shows "Register Now" button if seats available
- [ ] Shows "Fully Booked" if no seats

### Registration (`/register`)
- [ ] Page loads without errors
- [ ] Dropdown shows all upcoming retreats
- [ ] Can add up to 4 participants
- [ ] Form submits successfully
- [ ] Redirects to success page with booking ID

### Check Status (`/check-status`)
- [ ] Page loads without errors
- [ ] Can enter booking ID and WhatsApp number
- [ ] Shows booking details if found
- [ ] Shows error if not found

### Admin Login (`/login`)
- [ ] Page loads without errors
- [ ] Can login with credentials
- [ ] Redirects to dashboard after login

## 🐛 Common Issues & Solutions

### Issue: "Attempt to read property on array"
**Status:** ✅ FIXED
**Solution:** Updated views to use array syntax `$retreat['key']` instead of `$retreat->key`

### Issue: "Route [login] not defined"
**Status:** ✅ FIXED
**Solution:** Added login routes to web.php

### Issue: "View not found"
**Solution:**
```bash
php artisan view:clear
```

### Issue: "Class not found"
**Solution:**
```bash
composer dump-autoload
php artisan config:clear
```

### Issue: No retreats showing
**Solution:** Make sure you have active retreats in the database:
```bash
php artisan tinker
>>> App\Models\Retreat::where('is_active', true)->count();
>>> exit
```

## 📊 Data Format Reference

### API Response (Retreat List)
```php
[
    'retreat_id' => 1,
    'retreat_name' => 'Silent Retreat',
    'start_date' => '2024-11-01',
    'end_date' => '2024-11-03',
    'available_spots' => 15,
    'total_seats' => 20,
    'criteria' => 'open_to_all',
    'criteria_label' => 'Open to All'
]
```

### API Response (Retreat Details)
```php
[
    'retreat_id' => 1,
    'retreat_name' => 'Silent Retreat',
    'description' => '...',
    'start_date' => '2024-11-01',
    'end_date' => '2024-11-03',
    'timings' => '9 AM - 5 PM',
    'availability' => [
        'total_seats' => 20,
        'booked_seats' => 5,
        'available_spots' => 15,
        'is_available' => true
    ],
    'criteria' => [
        'type' => 'open_to_all',
        'label' => 'Open to All'
    ],
    'details' => [
        'instructions' => '...',
        'special_remarks' => '...'
    ]
]
```

## ✅ Final Checklist

Before deploying to production:

- [ ] All pages load without errors
- [ ] Can view retreats
- [ ] Can register for retreat
- [ ] Can check booking status
- [ ] Can login to admin
- [ ] Email confirmations work
- [ ] Database is backed up
- [ ] .env is configured correctly
- [ ] Apache virtual hosts configured
- [ ] SSL certificates installed

## 🚀 Ready for Production?

Once all tests pass locally:

1. Upload code to server
2. Update Apache virtual hosts (see FRONTEND_SETUP_GUIDE.md)
3. Run migrations: `php artisan migrate`
4. Clear cache: `php artisan optimize:clear`
5. Set permissions: `chmod -R 775 storage bootstrap/cache`
6. Test on production URLs

---

**Current Status:** ✅ All frontend routes use APIs, views updated, errors fixed
