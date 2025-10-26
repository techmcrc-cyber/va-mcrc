<?php

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

// Authenticated Admin Routes
Route::middleware(['auth:web'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
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
        
        // System settings (Super Admin only)
        Route::get('system', [\App\Http\Controllers\Admin\SettingController::class, 'system'])->name('system');
        Route::post('system', [\App\Http\Controllers\Admin\SettingController::class, 'storeSystemSetting'])->name('system.store');
        Route::delete('system/{id}', [\App\Http\Controllers\Admin\SettingController::class, 'deleteSystemSetting'])->name('system.delete');
        
        // Update settings
        Route::put('{type}', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        
        // Test email configuration
        Route::post('test-email', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('test-email');
    });

    // Support
    Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('admin.support');
    Route::post('/support', [\App\Http\Controllers\Admin\SupportController::class, 'submit'])->name('admin.support.submit');

    // Bookings Management
    Route::prefix('bookings')->name('admin.bookings.')->group(function () {
        // Import/Export Routes (must come before resource routes to avoid conflicts)
        Route::get('/import', [\App\Http\Controllers\Admin\BookingController::class, 'importForm'])->name('import');
        Route::post('/import', [\App\Http\Controllers\Admin\BookingController::class, 'processImport'])->name('import.process');
        Route::get('/import/template', [\App\Http\Controllers\Admin\BookingController::class, 'downloadTemplate'])->name('import.template');
        Route::post('/import/preview', [\App\Http\Controllers\Admin\BookingController::class, 'previewImport'])->name('import.preview');
        Route::post('/import/confirm', [\App\Http\Controllers\Admin\BookingController::class, 'confirmImport'])->name('import.confirm');
        
        Route::get('/export', [\App\Http\Controllers\Admin\BookingController::class, 'exportForm'])->name('export');
        Route::post('/export', [\App\Http\Controllers\Admin\BookingController::class, 'processExport'])->name('export.process');
        
        // Active and Archive Lists
        Route::get('/active', [\App\Http\Controllers\Admin\BookingController::class, 'active'])->name('active');
        Route::get('/archive', [\App\Http\Controllers\Admin\BookingController::class, 'archive'])->name('archive');
        
        // Standard CRUD routes
        Route::get('/', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\BookingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [\App\Http\Controllers\Admin\BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])->name('update');
        Route::delete('/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'destroy'])->name('destroy');
        Route::delete('/participant/{participant}', [\App\Http\Controllers\Admin\BookingController::class, 'cancelParticipant'])->name('cancel-participant');
        Route::post('/{booking}/send-confirmation', [\App\Http\Controllers\Admin\BookingController::class, 'sendConfirmation'])->name('send-confirmation');
    });
    
    // Users Management
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });
    
    // Retreats Management
    Route::prefix('retreats')->name('admin.retreats.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RetreatController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\RetreatController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\RetreatController::class, 'store'])->name('store');
        Route::get('/{retreat}', [\App\Http\Controllers\Admin\RetreatController::class, 'show'])->name('show');
        Route::get('/{retreat}/edit', [\App\Http\Controllers\Admin\RetreatController::class, 'edit'])->name('edit');
        Route::put('/{retreat}', [\App\Http\Controllers\Admin\RetreatController::class, 'update'])->name('update');
        Route::delete('/{retreat}', [\App\Http\Controllers\Admin\RetreatController::class, 'destroy'])->name('destroy');
    });

    // Criteria Management
    Route::prefix('criteria')->name('admin.criteria.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CriteriaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CriteriaController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CriteriaController::class, 'store'])->name('store');
        Route::get('/{criterion}/edit', [\App\Http\Controllers\Admin\CriteriaController::class, 'edit'])->name('edit');
        Route::put('/{criterion}', [\App\Http\Controllers\Admin\CriteriaController::class, 'update'])->name('update');
        Route::delete('/{criterion}', [\App\Http\Controllers\Admin\CriteriaController::class, 'destroy'])->name('destroy');
    });

    // Notifications Management
    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
        Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
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

    // Special Bookings (Super Admin Only)
    Route::prefix('special-bookings')->name('admin.special-bookings.')->middleware('can:super-admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'store'])->name('store');
        Route::get('/{specialBooking}', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'show'])->name('show');
        Route::get('/{specialBooking}/edit', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'edit'])->name('edit');
        Route::put('/{specialBooking}', [\App\Http\Controllers\Admin\SpecialBookingController::class, 'update'])->name('update');
    });
});
