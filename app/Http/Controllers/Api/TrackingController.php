<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Location;
use App\Events\DriverLocationUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    /**
     * Update driver's current location (called by driver's mobile app)
     */
    public function updateLocation(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Verify this is the assigned driver
        if (Auth::id() !== $order->driver_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        // Save the location
        $location = Location::create([
            'order_id' => $order->id,
            'driver_id' => Auth::id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'recorded_at' => now(),
        ]);

        // Broadcast the update to all tracking this order
        broadcast(new DriverLocationUpdated($order, $location));

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }

    /**
     * Get the latest location for an order
     */
    public function getLatestLocation($orderId)
    {
        $order = Order::findOrFail($orderId);
        $location = $order->latestLocation;

        return response()->json([
            'location' => $location,
            'driver' => $order->driver,
            'status' => $order->status,
        ]);
    }
}