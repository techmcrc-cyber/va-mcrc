# How to Login to Admin Panel

## Local Testing (Laragon)

### Login URL

```
http://localhost/retreatms/login
```

Or if using Laragon auto-domain:

```
http://retreatms.test/login
```

### Steps to Login

1. **Visit the login page:**
   ```
   http://localhost/retreatms/login
   ```

2. **Enter your credentials:**
   - Email: Your admin email
   - Password: Your admin password

3. **Click "Login"**

4. **You'll be redirected to:**
   ```
   http://localhost/retreatms/admin/dashboard
   ```

## Production (Live Server)

### Login URL

```
https://admin.myretreatbooking.com/login
```

### Steps to Login

1. **Visit:**
   ```
   https://admin.myretreatbooking.com/login
   ```

2. **Enter credentials and login**

3. **Redirected to:**
   ```
   https://admin.myretreatbooking.com/admin/dashboard
   ```

## Don't Have Admin Credentials?

### Create an Admin User

If you don't have an admin user yet, you can create one using Laravel Tinker:

```bash
cd E:\laragon\www\retreatms
php artisan tinker
```

Then run:

```php
$user = new App\Models\User();
$user->name = 'Admin User';
$user->email = 'admin@myretreatbooking.com';
$user->password = bcrypt('password123');
$user->is_active = true;
$user->save();
```

Press `Ctrl+C` to exit Tinker.

Now you can login with:
- **Email:** `admin@myretreatbooking.com`
- **Password:** `password123`

### Or Create via Database

If you have phpMyAdmin access:

1. Go to `http://localhost/phpmyadmin`
2. Select your database
3. Go to `users` table
4. Click "Insert"
5. Fill in:
   - `name`: Admin User
   - `email`: admin@myretreatbooking.com
   - `password`: Use this hash for "password123":
     ```
     $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
     ```
   - `is_active`: 1
   - `created_at`: Current timestamp
   - `updated_at`: Current timestamp

## After Login

Once logged in, you can access:

- **Dashboard:** `/admin/dashboard`
- **Bookings:** `/admin/bookings`
- **Retreats:** `/admin/retreats`
- **Users:** `/admin/users`
- **Settings:** `/admin/settings/general`

## Logout

Click the logout button in the admin panel, or visit:

```
POST http://localhost/retreatms/logout
```

## Troubleshooting

### Issue: "Route [login] not defined"

**Solution:** Clear route cache:
```bash
php artisan route:clear
php artisan config:clear
```

### Issue: "View [admin.auth.login] not found"

**Solution:** Check that the view exists:
```bash
dir resources\views\admin\auth\login.blade.php
```

### Issue: Login page shows but can't login

**Solution:** Check your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

### Issue: "These credentials do not match our records"

**Solutions:**
1. Make sure the user exists in the database
2. Check that `is_active = 1` in the users table
3. Verify the password is hashed correctly
4. Try creating a new user using Tinker (see above)

## Quick Test Commands

```bash
# Clear all caches
php artisan optimize:clear

# List all routes (check if login route exists)
php artisan route:list | findstr login

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Create test admin user
php artisan tinker
>>> App\Models\User::create(['name' => 'Test Admin', 'email' => 'test@admin.com', 'password' => bcrypt('password'), 'is_active' => true]);
>>> exit
```

## Summary

**Local Login URL:** `http://localhost/retreatms/login`

**Production Login URL:** `https://admin.myretreatbooking.com/login`

**Default Test Credentials (if you created them):**
- Email: `admin@myretreatbooking.com`
- Password: `password123`

---

**Important:** Change the default password after first login!
