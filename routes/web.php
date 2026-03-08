<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RetreatController as FrontendRetreatController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Multi-Tenant Routes (Organization Subdomains)
|--------------------------------------------------------------------------
| Routes for organization-specific subdomains: {org}.myretreatbooking.com
*/

Route::domain('{organization}.myretreatbooking.com')
    ->middleware('organization')
    ->group(function () {
        
        // Frontend Routes
        Route::get('/', [HomeController::class, 'index'])->name('org.home');
        Route::get('/retreats', [FrontendRetreatController::class, 'index'])->name('org.retreats.index');
        Route::get('/retreats/{id}', [FrontendRetreatController::class, 'show'])->name('org.retreats.show');
        Route::get('/register', [FrontendBookingController::class, 'create'])->name('org.booking.register');
        Route::post('/register', [FrontendBookingController::class, 'store'])->name('org.booking.store');
        Route::get('/booking-success', [FrontendBookingController::class, 'success'])->name('org.booking.success');
        Route::get('/check-status', [FrontendBookingController::class, 'checkStatusForm'])->name('org.booking.check-status');
        Route::post('/check-status', [FrontendBookingController::class, 'checkStatus'])->name('org.booking.check-status.submit');
        Route::post('/booking/cancel', [FrontendBookingController::class, 'cancelParticipant'])->name('org.booking.cancel');
        
        // Authentication Routes
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('org.login');
        Route::post('/login', [LoginController::class, 'login'])->name('org.login.submit');
        Route::post('/logout', [LoginController::class, 'logout'])->name('org.logout');
        
        // Admin Routes (Organization-scoped)
        Route::prefix('admin')->middleware('auth:web')->group(function () {
            require __DIR__.'/admin.php';
        });
    });

/*
|--------------------------------------------------------------------------
| Main Domain Routes (myretreatbooking.com)
|--------------------------------------------------------------------------
| Routes for the main domain - can be used for landing page or org selector
*/

Route::domain('myretreatbooking.com')->group(function () {
    
    // Main landing page
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Super Admin Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Super Admin Panel
    Route::prefix('admin')->middleware('auth:web')->group(function () {
        Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
        require __DIR__.'/admin.php';
    });
});

/*
|--------------------------------------------------------------------------
| Local Development Routes
|--------------------------------------------------------------------------
| Fallback routes for local development without domain configuration
*/

if (!in_array(request()->getHost(), ['myretreatbooking.com', 'www.myretreatbooking.com']) 
    && !str_contains(request()->getHost(), '.myretreatbooking.com')) {
    
    // Frontend Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/retreats', [FrontendRetreatController::class, 'index'])->name('retreats.index');
    Route::get('/retreats/{id}', [FrontendRetreatController::class, 'show'])->name('retreats.show');
    Route::get('/register', [FrontendBookingController::class, 'create'])->name('booking.register');
    Route::post('/register', [FrontendBookingController::class, 'store'])->name('booking.store');
    Route::get('/booking-success', [FrontendBookingController::class, 'success'])->name('booking.success');
    Route::get('/check-status', [FrontendBookingController::class, 'checkStatusForm'])->name('booking.check-status');
    Route::post('/check-status', [FrontendBookingController::class, 'checkStatus'])->name('booking.check-status.submit');
    Route::post('/booking/cancel', [FrontendBookingController::class, 'cancelParticipant'])->name('booking.cancel');
    
    // Authentication
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Admin Routes
    Route::prefix('admin')->middleware('auth:web')->group(function () {
        Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
        require __DIR__.'/admin.php';
    });
}
