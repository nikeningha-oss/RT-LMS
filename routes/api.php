<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================================
// ✅ TRACKING ROUTES
// ============================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // ─── DRIVER LOCATION (Real-time tracking) ───
    Route::get('/driver-location/{driverId}', [TrackingController::class, 'getDriverLocation']);
    Route::post('/tracking/{order}/location', [TrackingController::class, 'updateLocation']);
    
    // ─── ADMIN ORDER ROUTES ───
    // ✅ Get ALL online drivers (no distance filter) - Used in Assign Modal
    Route::get('/orders/{order}/all-drivers', [OrderController::class, 'getAllDrivers']);
    
    // ✅ Get order route data (pickup/dropoff for map)
    Route::get('/orders/{order}/route', [OrderController::class, 'getOrderRoute']);
    
    // ✅ Get nearby drivers within 10km
    Route::get('/orders/{order}/nearby-drivers', [OrderController::class, 'getNearbyDrivers']);
});