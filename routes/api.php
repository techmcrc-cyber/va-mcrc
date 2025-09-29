<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RetreatAPIController;
use App\Http\Controllers\API\BookingAPIController;

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
    
    Route::prefix('retreats')->group(function () {
        Route::get('/', [RetreatAPIController::class, 'index']);
        Route::get('/{id}', [RetreatAPIController::class, 'show']);
    });
    
    Route::prefix('bookings')->group(function () {
        Route::post('/', [BookingAPIController::class, 'store']);
        Route::get('/', [BookingAPIController::class, 'show']);
        Route::patch('/{id}/cancel', [BookingAPIController::class, 'cancel']);
    });
});
