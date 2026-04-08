<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RetreatController as FrontendRetreatController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Admin Routes (NOT organization-scoped)
|--------------------------------------------------------------------------
| Admin routes are at /admin/ level, separate from organization paths
*/

Route::prefix('admin')->middleware('auth:web')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    require __DIR__.'/admin.php';
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (NOT organization-scoped)
|--------------------------------------------------------------------------
| Login/Logout routes at root level
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Multi-Tenant Routes (Path-based)
|--------------------------------------------------------------------------
| Routes for organization-specific paths: /{organization}/...
| Examples: /all/, /org1/, /org2/
| The {organization} parameter can be:
| - 'all' = Show data from ALL organizations (no filtering)
| - 'org1', 'org2', etc. = Show ONLY that organization's data
*/

Route::prefix('{organization}')->middleware('organization')->group(function () {
    
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
});

/*
|--------------------------------------------------------------------------
| Root Routes (Redirect to /all/)
|--------------------------------------------------------------------------
| Redirect root URL to /all/ for consistency
*/

Route::get('/', function () {
    return redirect('/all/');
})->name('root');

Route::get('', function () {
    return redirect('/all/');
});

