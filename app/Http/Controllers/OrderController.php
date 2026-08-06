<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Show order creation form (Customer only)
     */
    public function create()
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can create orders.');
        }
        return view('customer.create-order');
    }

    /**
     * Store a new order with AUTO CALCULATION
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can create orders.');
        }

        $validator = Validator::make($request->all(), [
            'pickup_address' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
            'distance_km' => 'required|numeric|min:0.5',
            'weight_kg' => 'required|numeric|min:0.1',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => Auth::id(),
            'driver_id' => null,
            'vehicle_id' => null,
            'status' => 'pending',
            'payment_status' => 'pending',
            'pickup_address' => $request->pickup_address,
            'delivery_address' => $request->delivery_address,
            'pickup_lat' => $request->pickup_lat,
            'pickup_lng' => $request->pickup_lng,
            'delivery_lat' => $request->delivery_lat,
            'delivery_lng' => $request->delivery_lng,
            'distance_km' => $request->distance_km,
            'weight_kg' => $request->weight_kg,
            'description' => $request->description,
            'total_price' => 0,
        ]);

        $order->applyAutoPrice();

        return redirect()->route('customer.orders')
            ->with('success', '✅ Order #' . $order->order_number . ' created! Total: ' . number_format($order->total_price, 0, ',', ' ') . ' F');
    }

    /**
     * Show admin orders (Admin only)
     */
    public function adminOrders()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can view all orders.');
        }

        $orders = Order::with(['customer', 'driver', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'price_pending' => Order::where('status', 'price_pending')->count(),
            'in_transit' => Order::where('status', 'in_transit')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        return view('admin.orders', compact('orders', 'stats'));
    }

    /**
     * SHOW ASSIGN DRIVER FORM (Admin only)
     */
    public function showAssignDriver($orderId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can assign drivers.');
        }

        $order = Order::with('customer')->findOrFail($orderId);
        
        $availableDrivers = User::where('role', 'driver')
            ->whereHas('driver', function($query) {
                $query->where('is_available', true);
            })
            ->with('driver')
            ->get();
        
        $availableVehicles = Vehicle::where('is_active', true)
            ->where('status', 'available')
            ->get();
        
        return view('admin.orders-assign-driver', compact('order', 'availableDrivers', 'availableVehicles'));
    }

    /**
     * ASSIGN DRIVER TO ORDER (Admin only) - Supports both form submit and AJAX
     */
    public function assignDriver(Request $request, $orderId)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            $message = 'Unauthorized access. Only admins can assign drivers.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors()->first();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order = Order::findOrFail($orderId);

            // Check if payment is complete
            if (!$order->isPaid()) {
                $message = 'Cannot assign driver. Payment has not been completed.';
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Get the driver user
            $driverUser = User::findOrFail($request->driver_id);

            // Check if user is a driver
            if ($driverUser->role !== 'driver') {
                $message = 'Selected user is not a driver.';
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Check if driver has a driver profile
            $driver = Driver::where('user_id', $driverUser->id)->first();
            if (!$driver) {
                $message = 'This user does not have a driver profile.';
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Check if driver is available
            if (!$driver->is_available) {
                $message = 'This driver is currently not available.';
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Check if driver is already assigned to another active order
            $existingOrder = Order::where('driver_id', $driverUser->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->first();
            if ($existingOrder) {
                $message = 'Driver is already assigned to another active order (#' . $existingOrder->order_number . ').';
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            // Assign driver to order
            $order->driver_id = $driverUser->id;
            $order->status = Order::STATUS_ASSIGNED;
            $order->save();

            // Update driver availability
            $driver->is_available = false;
            $driver->save();

            Log::info('Driver assigned to order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'driver_id' => $driverUser->id,
                'driver_name' => $driverUser->name,
                'admin_id' => Auth::id()
            ]);

            $message = 'Driver assigned successfully to order #' . $order->order_number;

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->route('admin.orders')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Assign driver error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'driver_id' => $request->driver_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $message = 'Error assigning driver: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }

            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * SET ORDER PRICE (Admin only)
     */
    public function setPrice(Request $request, $orderId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can set prices.');
        }

        $validator = Validator::make($request->all(), [
            'total_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:approved,rejected',
            'distance_km' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0.1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $order = Order::findOrFail($orderId);

            if ($request->status === 'approved') {
                if ($request->has('distance_km')) {
                    $order->distance_km = $request->distance_km;
                }
                if ($request->has('weight_kg')) {
                    $order->weight_kg = $request->weight_kg;
                }
                
                $pricing = $order->calculatePrice();
                
                $order->update([
                    'total_price' => $pricing['total_price'],
                    'base_fare' => $pricing['base_fare'],
                    'distance_charge' => $pricing['distance_charge'],
                    'weight_charge' => $pricing['weight_charge'],
                    'service_fee' => $pricing['service_fee'],
                    'tax_rate' => $pricing['tax_rate'],
                    'tax_amount' => $pricing['tax_amount'],
                    'driver_earning' => $pricing['driver_earning'],
                    'driver_commission_rate' => $pricing['driver_commission_rate'],
                    'platform_fee' => $pricing['platform_fee'],
                    'status' => Order::STATUS_PRICE_PENDING,
                    'payment_status' => Order::PAYMENT_PENDING,
                ]);

                return redirect()->route('admin.orders')
                    ->with('success', '✅ Order #' . $order->order_number . ' approved with price: ' . number_format($pricing['total_price'], 0, ',', ' ') . ' FCFA. Awaiting customer confirmation.');
            } else {
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                ]);

                return redirect()->route('admin.orders')
                    ->with('error', '❌ Order #' . $order->order_number . ' rejected.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error processing order: ' . $e->getMessage());
        }
    }

    /**
     * CONFIRM PRICE (Customer only)
     */
    public function confirmPrice($orderId)
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can confirm prices.');
        }
        
        try {
            $order = Order::where('customer_id', Auth::id())
                ->where('id', $orderId)
                ->firstOrFail();
            
            if (!$order->canConfirmPrice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not waiting for price confirmation.'
                ], 400);
            }
            
            $order->confirmPrice();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Price confirmed! You can now proceed to payment.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SHOW PAYMENT PAGE (Customer only)
     */
    public function showPayment($orderId)
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can make payments.');
        }
        
        $order = Order::where('customer_id', Auth::id())
            ->where('id', $orderId)
            ->firstOrFail();
        
        if (!$order->canPay()) {
            return redirect()->route('customer.orders')
                ->with('error', 'Order is not ready for payment.');
        }
        
        return view('customer.payment', compact('order'));
    }

    /**
     * PROCESS PAYMENT (Customer only)
     */
    public function processPayment(Request $request, $orderId)
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can make payments.');
        }
        
        try {
            $order = Order::where('customer_id', Auth::id())
                ->where('id', $orderId)
                ->firstOrFail();
            
            if (!$order->canPay()) {
                return redirect()->route('customer.orders')
                    ->with('error', 'Order is not ready for payment.');
            }
            
            $order->markAsPaid();
            
            return redirect()->route('customer.orders')
                ->with('success', '✅ Payment successful! Your order is now being processed.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Update order status (Driver or Admin)
     */
    public function updateStatus(Request $request, $orderId)
    {
        $user = Auth::user();

        if ($user->role === 'customer') {
            abort(403, 'Customers cannot update order status.');
        }

        $order = Order::findOrFail($orderId);

        if ($user->role === 'driver' && $order->driver_id !== $user->id) {
            abort(403, 'You are not assigned to this order.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            $order->status = $request->status;
            
            if ($request->status === 'delivered') {
                $order->actual_delivery = now();
                
                if ($order->driver_id) {
                    $driver = User::find($order->driver_id);
                    if ($driver && $driver->driver) {
                        $driver->driver->is_available = true;
                        $driver->driver->save();
                    }
                }
                
                if ($order->vehicle_id) {
                    $vehicle = Vehicle::find($order->vehicle_id);
                    if ($vehicle) {
                        $vehicle->status = 'available';
                        $vehicle->driver_id = null;
                        $vehicle->save();
                    }
                }
            }
            
            $order->save();

            $message = '✅ Order #' . $order->order_number . ' status updated to: ' . ucfirst(str_replace('_', ' ', $request->status));

            if ($user->role === 'admin') {
                return redirect()->route('admin.orders')->with('success', $message);
            } else {
                return redirect()->route('driver.orders')->with('success', $message);
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Show customer orders (Customer only)
     */
    public function customerOrders()
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Only customers can view their orders.');
        }

        $orders = Order::where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show driver orders (Driver only)
     */
    public function driverOrders()
    {
        if (Auth::user()->role !== 'driver') {
            abort(403, 'Only drivers can view their orders.');
        }

        $orders = Order::where('driver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.orders', compact('orders'));
    }

    /**
     * Get available drivers for assignment (Admin only) - AJAX
     */
    public function getAvailableDrivers()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $drivers = User::where('role', 'driver')
            ->whereHas('driver', function($query) {
                $query->where('is_available', true);
            })
            ->with('driver')
            ->get();
            
        return response()->json($drivers);
    }

    /**
     * Get available vehicles for assignment (Admin only) - AJAX
     */
    public function getAvailableVehicles()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $vehicles = Vehicle::where('is_active', true)
            ->where('status', 'available')
            ->get();
            
        return response()->json($vehicles);
    }

    /**
     * Get order route data for the map (Admin)
     */
    public function getOrderRoute($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'pickup_lat' => $order->pickup_lat,
                    'pickup_lng' => $order->pickup_lng,
                    'delivery_lat' => $order->delivery_lat,
                    'delivery_lng' => $order->delivery_lng,
                    'pickup_address' => $order->pickup_address,
                    'delivery_address' => $order->delivery_address,
                    'status' => $order->status,
                    'driver_id' => $order->driver_id,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching order route: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ALL online drivers (not just nearby)
     */
    public function getAllDrivers($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            $drivers = Driver::with(['user', 'vehicle'])
                ->where('is_available', true)
                ->get();
            
            Log::info('Fetching all online drivers', [
                'order_id' => $orderId,
                'total_drivers' => $drivers->count()
            ]);
            
            $driversWithDistance = $drivers->map(function($driver) use ($order) {
                $distance = null;
                if ($order->pickup_lat && $order->pickup_lng && $driver->current_lat && $driver->current_lng) {
                    $distance = $this->calculateDistance(
                        (float)$order->pickup_lat,
                        (float)$order->pickup_lng,
                        (float)$driver->current_lat,
                        (float)$driver->current_lng
                    );
                }
                
                return [
                    'id' => $driver->user_id,
                    'user_id' => $driver->user_id,
                    'name' => $driver->name ?? $driver->user->name ?? 'Unknown',
                    'phone' => $driver->phone ?? $driver->user->phone ?? 'N/A',
                    'email' => $driver->email ?? $driver->user->email ?? 'N/A',
                    'vehicle' => $driver->vehicle ? [
                        'model' => $driver->vehicle->model,
                        'plate_number' => $driver->vehicle->plate_number,
                        'type' => $driver->vehicle->type,
                    ] : null,
                    'current_lat' => $driver->current_lat ? (float)$driver->current_lat : null,
                    'current_lng' => $driver->current_lng ? (float)$driver->current_lng : null,
                    'distance' => $distance !== null ? round($distance, 2) : null,
                    'is_available' => (bool)$driver->is_available,
                    'last_known_location_at' => $driver->last_known_location_at,
                ];
            })->sortBy(function($driver) {
                return $driver['distance'] ?? 99999;
            })->values();
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'pickup_lat' => $order->pickup_lat ? (float)$order->pickup_lat : null,
                    'pickup_lng' => $order->pickup_lng ? (float)$order->pickup_lng : null,
                    'delivery_lat' => $order->delivery_lat ? (float)$order->delivery_lat : null,
                    'delivery_lng' => $order->delivery_lng ? (float)$order->delivery_lng : null,
                    'pickup_address' => $order->pickup_address,
                    'delivery_address' => $order->delivery_address,
                ],
                'drivers' => $driversWithDistance,
                'total_drivers' => $driversWithDistance->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching drivers: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching drivers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nearby drivers (within 10km)
     */
    public function getNearbyDrivers($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            if (!$order->pickup_lat || !$order->pickup_lng) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order does not have pickup coordinates'
                ], 400);
            }
            
            $drivers = Driver::with(['user', 'vehicle'])
                ->where('is_available', true)
                ->get();
            
            $driversWithDistance = $drivers->map(function($driver) use ($order) {
                $distance = null;
                if ($driver->current_lat && $driver->current_lng) {
                    $distance = $this->calculateDistance(
                        (float)$order->pickup_lat,
                        (float)$order->pickup_lng,
                        (float)$driver->current_lat,
                        (float)$driver->current_lng
                    );
                }
                
                return [
                    'id' => $driver->user_id,
                    'user_id' => $driver->user_id,
                    'name' => $driver->name ?? $driver->user->name ?? 'Unknown',
                    'phone' => $driver->phone ?? $driver->user->phone ?? 'N/A',
                    'email' => $driver->email ?? $driver->user->email ?? 'N/A',
                    'vehicle' => $driver->vehicle ? [
                        'model' => $driver->vehicle->model,
                        'plate_number' => $driver->vehicle->plate_number,
                        'type' => $driver->vehicle->type,
                    ] : null,
                    'current_lat' => $driver->current_lat ? (float)$driver->current_lat : null,
                    'current_lng' => $driver->current_lng ? (float)$driver->current_lng : null,
                    'distance' => $distance !== null ? round($distance, 2) : null,
                    'is_available' => (bool)$driver->is_available,
                    'last_known_location_at' => $driver->last_known_location_at,
                ];
            })->filter(function($driver) {
                if ($driver['distance'] === null) {
                    return true;
                }
                return $driver['distance'] <= 10;
            })->sortBy(function($driver) {
                return $driver['distance'] ?? 99999;
            })->values();
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'pickup_lat' => $order->pickup_lat ? (float)$order->pickup_lat : null,
                    'pickup_lng' => $order->pickup_lng ? (float)$order->pickup_lng : null,
                    'delivery_lat' => $order->delivery_lat ? (float)$order->delivery_lat : null,
                    'delivery_lng' => $order->delivery_lng ? (float)$order->delivery_lng : null,
                    'pickup_address' => $order->pickup_address,
                    'delivery_address' => $order->delivery_address,
                ],
                'drivers' => $driversWithDistance,
                'total_nearby' => $driversWithDistance->count(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching nearby drivers: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching nearby drivers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver location for tracking
     */
    public function getDriverLocation($driverId)
    {
        try {
            $driver = Driver::with('user')
                ->where('user_id', $driverId)
                ->first();
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $user = Auth::user();
            $isAuthorized = false;
            
            if ($user->role === 'admin') {
                $isAuthorized = true;
            } elseif ($user->role === 'driver' && $user->id == $driverId) {
                $isAuthorized = true;
            } elseif ($user->role === 'customer') {
                $hasOrder = Order::where('customer_id', $user->id)
                    ->where('driver_id', $driverId)
                    ->whereNotIn('status', ['delivered', 'cancelled'])
                    ->exists();
                $isAuthorized = $hasOrder;
            }
            
            if (!$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'latitude' => $driver->current_lat,
                'longitude' => $driver->current_lng,
                'speed' => $driver->current_speed ?? 0,
                'is_available' => $driver->is_available,
                'last_updated' => $driver->last_known_location_at,
                'eta' => null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching driver location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching driver location'
            ], 500);
        }
    }

    /**
     * Update driver location (Web route)
     */
    public function updateDriverLocation(Request $request, $orderId)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'driver') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only drivers can update location'
                ], 403);
            }
            
            $order = Order::where('id', $orderId)
                ->where('driver_id', $user->id)
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or not assigned to you'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'speed' => 'nullable|numeric|min:0',
                'heading' => 'nullable|numeric|between:0,360',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            $driver = Driver::where('user_id', $user->id)->first();
            if ($driver) {
                $driver->update([
                    'current_lat' => $request->latitude,
                    'current_lng' => $request->longitude,
                    'current_speed' => $request->speed ?? 0,
                    'last_known_location_at' => now(),
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating driver location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating location'
            ], 500);
        }
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