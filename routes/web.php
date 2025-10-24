<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RetreatController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\WelcomeController;

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

Route::domain('admin.myretreatbooking.com')->group(function () {

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    // Login routes (guest only)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Admin routes (authenticated)
    Route::prefix('admin')->group(function () {
        require __DIR__.'/admin.php';
    });
});


/*
|--------------------------------------------------------------------------
| Web Routes - Local Developments
|--------------------------------------------------------------------------
*/

if (!in_array(request()->getHost(), ['myretreatbooking.com', 'www.myretreatbooking.com', 'admin.myretreatbooking.com'])) {

    Route::get('/system', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/retreats', [RetreatController::class, 'index'])->name('retreats.index');
    Route::get('/retreats/{id}', [RetreatController::class, 'show'])->name('retreats.show');
    Route::get('/register', [BookingController::class, 'create'])->name('booking.register');
    Route::post('/register', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking-success', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/check-status', [BookingController::class, 'checkStatusForm'])->name('booking.check-status');
    Route::post('/check-status', [BookingController::class, 'checkStatus'])->name('booking.check-status.submit');
    
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::prefix('admin')->group(function () {
        require __DIR__.'/admin.php';
    });
}
