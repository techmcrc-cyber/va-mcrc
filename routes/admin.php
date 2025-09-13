<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group and is prefixed with 'admin'.
|
*/

// Authentication Routes (Public)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
});

// Authenticated Admin Routes
Route::middleware(['auth:web'])->group(function () {
    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // User Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

    // Settings
    Route::prefix('settings')->name('admin.settings.')->group(function () {
        // View settings pages
        Route::get('general', [\App\Http\Controllers\Admin\SettingController::class, 'general'])->name('general');
        Route::get('email', [\App\Http\Controllers\Admin\SettingController::class, 'email'])->name('email');
        Route::get('payment', [\App\Http\Controllers\Admin\SettingController::class, 'payment'])->name('payment');
        Route::get('notification', [\App\Http\Controllers\Admin\SettingController::class, 'notification'])->name('notification');
        
        // Update settings
        Route::put('{type}', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        
        // Test email configuration
        Route::post('test-email', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('test-email');
    });

    // Support
    Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support');
    Route::post('/support', [\App\Http\Controllers\Admin\SupportController::class, 'submit'])->name('admin.support.submit');

    // Users Management
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });

    // Roles Management
    Route::prefix('roles')->name('admin.roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('destroy');
    });

    // Permissions Management
    Route::prefix('permissions')->name('admin.permissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('store');
        Route::get('/{permission}/edit', [\App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('destroy');
    });
});
