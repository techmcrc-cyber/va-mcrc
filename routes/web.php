<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RetreatController;
use App\Http\Controllers\Frontend\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Frontend (myretreatbooking.com)
|--------------------------------------------------------------------------
*/

Route::domain('myretreatbooking.com')->group(function () {
    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Retreats
    Route::get('/retreats', [RetreatController::class, 'index'])->name('retreats.index');
    Route::get('/retreats/{id}', [RetreatController::class, 'show'])->name('retreats.show');
    
    // Booking/Registration
    Route::get('/register', [BookingController::class, 'create'])->name('booking.register');
    Route::post('/register', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking-success', [BookingController::class, 'success'])->name('booking.success');
    
    // Check Status
    Route::get('/check-status', [BookingController::class, 'checkStatusForm'])->name('booking.check-status');
    Route::post('/check-status', [BookingController::class, 'checkStatus'])->name('booking.check-status.submit');
});

/*
|--------------------------------------------------------------------------
| Web Routes - Admin (admin.myretreatbooking.com)
|--------------------------------------------------------------------------
*/

Route::domain('admin.myretreatbooking.com')->prefix('admin')->group(function () {
    // Include admin routes
    require __DIR__.'/admin.php';
    
    // Include auth routes
    require __DIR__.'/auth.php';
});

/*
|--------------------------------------------------------------------------
| Fallback for local development (no domain routing)
|--------------------------------------------------------------------------
*/

// If accessing without domain (localhost), show frontend
Route::get('/', [HomeController::class, 'index'])->name('home.fallback');
Route::get('/retreats', [RetreatController::class, 'index'])->name('retreats.index.fallback');
Route::get('/retreats/{id}', [RetreatController::class, 'show'])->name('retreats.show.fallback');
Route::get('/register', [BookingController::class, 'create'])->name('booking.register.fallback');
Route::post('/register', [BookingController::class, 'store'])->name('booking.store.fallback');
Route::get('/booking-success', [BookingController::class, 'success'])->name('booking.success.fallback');
Route::get('/check-status', [BookingController::class, 'checkStatusForm'])->name('booking.check-status.fallback');
Route::post('/check-status', [BookingController::class, 'checkStatus'])->name('booking.check-status.submit.fallback');
