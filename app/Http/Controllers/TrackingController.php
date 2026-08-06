<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrackingController extends Controller
{
    /**
     * Show tracking page
     */
    public function index($orderId = null)
    {
        $user = Auth::user();
        $order = null;
        $trackingData = null;
        $allOrders = [];

        if ($orderId) {
            $order = Order::with(['customer', 'driver', 'driverProfile', 'driverProfile.vehicle'])
                ->find($orderId);
        } elseif ($user->role === 'customer') {
            $order = Order::where('customer_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->with(['customer', 'driver', 'driverProfile', 'driverProfile.vehicle'])
                ->first();
        } elseif ($user->role === 'driver') {
            $order = Order::where('driver_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->with(['customer', 'driver', 'driverProfile', 'driverProfile.vehicle'])
                ->first();
        } elseif ($user->role === 'admin') {
            $allOrders = Order::with(['customer', 'driver', 'driverProfile', 'driverProfile.vehicle'])
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->get();
            
            if ($allOrders->count() > 0) {
                $order = $allOrders->first();
            }
        }

        if ($order) {
            $driverData = null;
            if ($order->driver) {
                $driverProfile = $order->driverProfile;
                
                $driverData = [
                    'name' => $order->driver->name ?? 'Driver',
                    'phone' => $order->driver->phone ?? 'N/A',
                    'vehicle' => $driverProfile && $driverProfile->vehicle ? $driverProfile->vehicle->plate_number : 'Vehicle',
                    'lat' => $driverProfile ? $driverProfile->current_lat : null,
                    'lng' => $driverProfile ? $driverProfile->current_lng : null,
                    'is_available' => $driverProfile ? $driverProfile->is_available : false,
                    // ✅ IMPORTANT: Pass the user_id for location polling
                    'user_id' => $order->driver->id
                ];
            }

            $trackingData = [
                'id' => $order->order_number,
                'status' => $order->status,
                'pickup' => [
                    'lat' => $order->pickup_lat ?? 4.0511,
                    'lng' => $order->pickup_lng ?? 9.7679,
                    'label' => $order->pickup_address ?? 'Pickup Location'
                ],
                'dropoff' => [
                    'lat' => $order->delivery_lat ?? 4.0469,
                    'lng' => $order->delivery_lng ?? 9.7679,
                    'label' => $order->delivery_address ?? 'Delivery Location'
                ],
                'driver' => $driverData,
                'customer' => $order->customer ? [
                    'name' => $order->customer->name ?? 'Customer',
                    'phone' => $order->customer->phone ?? 'N/A'
                ] : null,
                'statusLog' => [
                    ['title' => 'Order Requested', 'done' => true, 'time' => $order->created_at ? $order->created_at->format('h:i A') : ''],
                    ['title' => 'Driver Assigned', 'done' => $order->driver_id ? true : false, 'time' => $order->updated_at ? $order->updated_at->format('h:i A') : ''],
                    ['title' => 'Picked Up', 'done' => in_array($order->status, ['picked_up', 'in_transit', 'delivered']), 'time' => ''],
                    ['title' => 'In Transit', 'done' => in_array($order->status, ['in_transit', 'delivered']), 'time' => ''],
                    ['title' => 'Delivered', 'done' => $order->status === 'delivered', 'time' => $order->actual_delivery ? $order->actual_delivery->format('h:i A') : '']
                ]
            ];
        }

        return view('tracking.index', compact('order', 'trackingData', 'allOrders'));
    }

    /**
     * GET DRIVER LOCATION - For real-time tracking
     * ✅ FIXED: Uses user_id (not driver table id)
     */
    public function getDriverLocation($userId)
    {
        try {
            Log::info('📍 getDriverLocation called', ['user_id' => $userId]);
            
            // ✅ Find driver by user_id
            $driver = Driver::with(['user', 'vehicle'])
                ->where('user_id', $userId)
                ->first();
            
            if (!$driver) {
                Log::warning('❌ Driver not found for user_id: ' . $userId);
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }

            Log::info('✅ Driver found', [
                'driver_id' => $driver->id,
                'user_id' => $driver->user_id,
                'lat' => $driver->current_lat,
                'lng' => $driver->current_lng
            ]);

            // Get active order for ETA calculation
            $activeOrder = Order::where('driver_id', $driver->user_id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->first();

            $eta = null;
            if ($activeOrder && $driver->current_lat && $driver->current_lng) {
                $eta = $this->calculateETA(
                    $driver->current_lat,
                    $driver->current_lng,
                    $activeOrder->delivery_lat ?? $driver->current_lat,
                    $activeOrder->delivery_lng ?? $driver->current_lng
                );
            }

            return response()->json([
                'success' => true,
                'latitude' => $driver->current_lat,
                'longitude' => $driver->current_lng,
                'is_available' => $driver->is_available,
                'speed' => $driver->current_speed ?? 0,
                'eta' => $eta,
                'driver_name' => $driver->user ? $driver->user->name : 'Driver',
                'last_updated' => $driver->last_known_location_at
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getting driver location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching driver location'
            ], 500);
        }
    }

    /**
     * UPDATE DRIVER LOCATION - Driver sends location
     */
    public function updateLocation(Request $request, $orderId)
    {
        try {
            Log::info('📍 updateLocation called', [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'role' => Auth::user()->role
            ]);

            $user = Auth::user();
            
            if ($user->role !== 'driver') {
                Log::warning('❌ Non-driver tried to update location');
                return response()->json([
                    'success' => false,
                    'message' => 'Only drivers can update location'
                ], 403);
            }

            $order = Order::find($orderId);
            if (!$order || $order->driver_id !== $user->id) {
                Log::warning('❌ Order not found or not assigned to driver');
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or not assigned to you'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'speed' => 'nullable|numeric|min:0',
                'heading' => 'nullable|numeric|min:0|max:360',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // ✅ Update driver location
            $driver = Driver::where('user_id', $user->id)->first();
            if ($driver) {
                $driver->current_lat = $request->latitude;
                $driver->current_lng = $request->longitude;
                $driver->current_speed = $request->speed ?? 0;
                $driver->last_known_location_at = now();
                $driver->save();

                Log::info('✅ Driver location updated', [
                    'driver_id' => $driver->id,
                    'user_id' => $user->id,
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Location updated successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('❌ Error updating location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating location'
            ], 500);
        }
    }

    /**
     * Calculate ETA
     */
    private function calculateETA($lat1, $lng1, $lat2, $lng2)
    {
        $distance = $this->calculateDistance($lat1, $lng1, $lat2, $lng2);
        return max(1, round($distance / 25 * 60));
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }
}