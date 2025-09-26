<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RetreatAPIController;
use App\Http\Controllers\API\BookingAPIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Health check endpoint (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Protected API routes with authentication
Route::middleware(['api.auth'])->group(function () {
    
    // Retreat endpoints
    Route::prefix('retreats')->group(function () {
        // List available retreats
        Route::get('/', [RetreatAPIController::class, 'index']);
        
        // Get retreat details by ID
        Route::get('/{id}', [RetreatAPIController::class, 'show']);
    });
    
    // Booking endpoints
    Route::prefix('bookings')->group(function () {
        // Create new booking
        Route::post('/', [BookingAPIController::class, 'store']);
        
        // View booking details
        Route::get('/', [BookingAPIController::class, 'show']);
        
        // Partially cancel booking
        Route::patch('/{id}/cancel', [BookingAPIController::class, 'cancel']);
    });
});
