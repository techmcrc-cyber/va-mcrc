<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RetreatController as FrontendRetreatController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Retreats (Frontend)
Route::get('/retreats', [FrontendRetreatController::class, 'index'])->name('retreats.index');
Route::get('/retreats/{id}', [FrontendRetreatController::class, 'show'])->name('retreats.show');

// Booking/Registration
Route::get('/register', [FrontendBookingController::class, 'create'])->name('booking.register');
Route::post('/register', [FrontendBookingController::class, 'store'])->name('booking.store');
Route::get('/booking-success', [FrontendBookingController::class, 'success'])->name('booking.success');

// Check Status
Route::get('/check-status', [FrontendBookingController::class, 'checkStatusForm'])->name('booking.check-status');
Route::post('/check-status', [FrontendBookingController::class, 'checkStatus'])->name('booking.check-status.submit');

// Cancellation
Route::post('/booking/cancel', [FrontendBookingController::class, 'cancelParticipant'])->name('booking.cancel');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    require __DIR__.'/admin.php';
});
