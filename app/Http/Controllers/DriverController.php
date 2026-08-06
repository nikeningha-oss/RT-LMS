<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\DriverPayment;
use App\Models\Earning;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverController extends Controller
{
    // ============================================================
    // 1. DASHBOARD & INDEX
    // ============================================================

    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $user = Auth::user();
        $driverId = $user->id;
        
        $completedOrders = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->get();
        
        $totalCompleted = $completedOrders->count();
        $totalEarnings = $completedOrders->sum('driver_earning');
        
        $completedToday = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();
        
        $todayEarnings = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('driver_earning');
        
        $activeOrder = Order::where('driver_id', $driverId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with('customer')
            ->first();
        
        $recentCompleted = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->with('customer')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $driverProfile = $user->driver;
        $avgPerDelivery = ($totalCompleted > 0) ? ($totalEarnings / $totalCompleted) : 0;

        return view('dashboard.driver', compact(
            'activeOrder',
            'completedToday',
            'todayEarnings',
            'totalCompleted',
            'totalEarnings',
            'recentCompleted',
            'driverProfile',
            'avgPerDelivery'
        ));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('driver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('driver.orders', compact('orders'));
    }

    // ============================================================
    // ✅ FIXED: updateStatus() - No redirect()->back()
    // ============================================================
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $user = Auth::user();
            
            Log::info('🔄 Driver status update called', [
                'driver_id' => $user->id,
                'order_id' => $order->id,
                'order_driver_id' => $order->driver_id,
                'new_status' => $request->status,
                'is_ajax' => $request->ajax() || $request->expectsJson()
            ]);
            
            if ($order->driver_id !== $user->id) {
                $errorMessage = 'You are not assigned to this order.';
                
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 403);
                }
                
                // ✅ FIXED: Explicit redirect
                return redirect()->route('driver.dashboard')->with('error', $errorMessage);
            }
            
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,cancelled',
            ]);
            
            if ($validator->fails()) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors()->first()
                    ], 422);
                }
                
                // ✅ FIXED: Explicit redirect
                return redirect()->route('driver.dashboard')->withErrors($validator);
            }
            
            $oldStatus = $order->status;
            $order->status = $request->status;
            
            if ($request->status === 'delivered') {
                $order->actual_delivery = now();
                
                $subtotal = ($order->base_fare ?? 0) + 
                           ($order->distance_charge ?? 0) + 
                           ($order->weight_charge ?? 0);
                
                $driverEarning = $subtotal * 0.5;
                $platformFee = $subtotal * 0.5;
                
                $order->driver_earning = $driverEarning;
                $order->platform_fee = $platformFee;
                
                Log::info('💰 Earnings calculation for delivery', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'subtotal' => $subtotal,
                    'driver_earning' => $driverEarning,
                    'platform_fee' => $platformFee,
                ]);
                
                $driver = $user->driver;
                if ($driver) {
                    $driver->addEarnings($driverEarning);
                    
                    Log::info('✅ Driver earnings updated after delivery', [
                        'driver_id' => $driver->id,
                        'order_id' => $order->id,
                        'earning_amount' => $driverEarning,
                        'new_total_earned' => $driver->total_earned,
                        'new_balance' => $driver->available_balance
                    ]);
                }
            }
            
            $order->save();
            
            Log::info('✅ Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $order->status
            ]);
            
            $message = '✅ Status updated to ' . ucfirst(str_replace('_', ' ', $request->status));
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'status' => $order->status,
                    'order' => $order
                ]);
            }
            
            // ✅ FIXED: Explicit redirect
            return redirect()->route('driver.dashboard')->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('❌ Error updating status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating status: ' . $e->getMessage()
                ], 500);
            }
            
            // ✅ FIXED: Explicit redirect
            return redirect()->route('driver.dashboard')->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ✅ FIXED: toggleStatus() - No redirect()->back()
    // ============================================================
    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if ($driver) {
            $driver->is_available = !$driver->is_available;
            $driver->save();
            
            $status = $driver->is_available ? 'online' : 'offline';
            
            // ✅ FIXED: Explicit redirect
            return redirect()->route('driver.dashboard')->with('success', "You are now {$status}.");
        }
        
        // ✅ FIXED: Explicit redirect
        return redirect()->route('driver.dashboard')->with('error', 'Driver profile not found.');
    }

    public function location()
    {
        $user = Auth::user();
        $driver = $user->driver;

        if (!$driver) {
            return response()->json(['error' => 'Driver not found'], 404);
        }

        return response()->json([
            'lat' => $driver->current_lat,
            'lng' => $driver->current_lng,
            'is_available' => $driver->is_available,
        ]);
    }

    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can view all drivers.');
        }
        
        $drivers = Driver::with(['user', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.drivers', compact('drivers'));
    }

    // ============================================================
    // ✅ FIXED: store() - No redirect()->back()
    // ============================================================
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can create drivers.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            // ✅ FIXED: Explicit redirect
            return redirect()->route('admin.drivers')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'driver',
                'is_available' => $request->has('is_available'),
            ]);

            $driver = Driver::create([
                'user_id' => $user->id,
                'phone' => $request->phone ?? '',
                'license_number' => $request->license_number ?? 'PENDING-' . $user->id,
                'is_available' => $request->has('is_available'),
                'available_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
            ]);

            if ($request->filled('vehicle_model') || $request->filled('plate_number')) {
                $vehicle = Vehicle::create([
                    'model' => $request->vehicle_model ?? 'Unknown',
                    'plate_number' => $request->plate_number ?? 'N/A',
                    'driver_id' => $driver->id,
                    'is_active' => true,
                ]);
                $driver->update(['vehicle_id' => $vehicle->id]);
            }
            
            DB::commit();

            return redirect()->route('admin.drivers')
                ->with('success', '✅ Driver created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            // ✅ FIXED: Explicit redirect
            return redirect()->route('admin.drivers')
                ->with('error', 'Error creating driver: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================================================
    // ✅ GET EDIT DATA - Uses driver ID
    // ============================================================
    public function getEditData($driverId)
    {
        try {
            $driver = Driver::with(['user', 'vehicle'])
                ->find($driverId);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $user = $driver->user;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this driver'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'driver' => [
                    'id' => $driver->id,
                    'user_id' => $user->id,
                    'name' => $user->name ?? 'N/A',
                    'email' => $user->email ?? 'N/A',
                    'phone' => $driver->phone ?? '',
                    'license_number' => $driver->license_number ?? '',
                    'is_available' => (bool) $driver->is_available,
                    'vehicle_id' => $driver->vehicle_id,
                    'vehicle' => $driver->vehicle ? [
                        'id' => $driver->vehicle->id,
                        'model' => $driver->vehicle->model ?? '',
                        'plate_number' => $driver->vehicle->plate_number ?? '',
                    ] : null,
                    'total_earned' => $driver->total_earned ?? 0,
                    'available_balance' => $driver->available_balance ?? 0,
                    'total_withdrawn' => $driver->total_withdrawn ?? 0,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching driver data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading driver data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // ✅ FIXED: update() - No redirect()->back()
    // ============================================================
    public function update(Request $request, $driverId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can update drivers.');
        }

        try {
            Log::info('🔵 UPDATE DRIVER CALLED', [
                'driver_id' => $driverId,
                'request_all' => $request->all()
            ]);

            $driver = Driver::with('user')->find($driverId);
            
            if (!$driver) {
                Log::error('❌ Driver not found with ID: ' . $driverId);
                // ✅ FIXED: Explicit redirect
                return redirect()->route('admin.drivers')
                    ->with('error', 'Driver not found with ID: ' . $driverId);
            }

            $user = $driver->user;
            
            if (!$user) {
                Log::error('❌ User not found for driver ID: ' . $driverId);
                // ✅ FIXED: Explicit redirect
                return redirect()->route('admin.drivers')
                    ->with('error', 'User not found for this driver.');
            }

            if ($user->role !== 'driver') {
                Log::error('❌ User is not a driver: ' . $user->id);
                // ✅ FIXED: Explicit redirect
                return redirect()->route('admin.drivers')
                    ->with('error', 'User is not a driver');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'license_number' => 'nullable|string|max:50',
                'is_available' => 'nullable|in:0,1,on,off',
                'vehicle_id' => 'nullable|exists:vehicles,id',
            ]);

            if ($validator->fails()) {
                Log::error('❌ Validation failed: ', $validator->errors()->toArray());
                // ✅ FIXED: Explicit redirect
                return redirect()->route('admin.drivers')
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            Log::info('✅ User updated: ', ['user_id' => $user->id]);

            $isAvailable = false;
            if ($request->has('is_available')) {
                $isAvailable = $request->is_available == '1' || $request->is_available == 'on' || $request->is_available === true;
            }

            $driverData = [
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'is_available' => $isAvailable,
            ];

            Log::info('🔵 Driver data to update: ', $driverData);

            $driver->update($driverData);

            Log::info('✅ Driver updated: ', [
                'driver_id' => $driver->id,
                'new_license_number' => $driver->license_number,
                'phone' => $driver->phone,
                'is_available' => $driver->is_available
            ]);

            if ($request->filled('vehicle_id') && $request->vehicle_id != '') {
                if ($driver->vehicle_id) {
                    Vehicle::where('id', $driver->vehicle_id)->update(['driver_id' => null]);
                }
                
                $driver->update(['vehicle_id' => $request->vehicle_id]);
                Vehicle::where('id', $request->vehicle_id)->update(['driver_id' => $driver->id]);
                
                Log::info('✅ Vehicle assigned: ', ['vehicle_id' => $request->vehicle_id]);
            } else {
                if ($driver->vehicle_id) {
                    Vehicle::where('id', $driver->vehicle_id)->update(['driver_id' => null]);
                }
                $driver->update(['vehicle_id' => null]);
                Log::info('✅ Vehicle unassigned');
            }

            DB::commit();

            $updatedDriver = Driver::with('user', 'vehicle')->find($driverId);
            Log::info('✅ VERIFICATION - Driver after update: ', [
                'id' => $updatedDriver->id,
                'license_number' => $updatedDriver->license_number,
                'phone' => $updatedDriver->phone,
                'is_available' => $updatedDriver->is_available,
                'vehicle_id' => $updatedDriver->vehicle_id
            ]);

            return redirect()->route('admin.drivers')
                ->with('success', '✅ Driver updated successfully! License: ' . ($updatedDriver->license_number ?? 'N/A'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error updating driver: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'driver_id' => $driverId
            ]);
            // ✅ FIXED: Explicit redirect
            return redirect()->route('admin.drivers')
                ->with('error', 'Error updating driver: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================================================
    // TOGGLE STATUS ADMIN
    // ============================================================
    public function toggleStatusAdmin($driverId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can toggle driver status.'
            ], 403);
        }

        try {
            $driver = Driver::with('user')->findOrFail($driverId);
            
            $driver->is_available = !$driver->is_available;
            $driver->save();

            return response()->json([
                'success' => true,
                'is_available' => $driver->is_available,
                'message' => $driver->is_available ? 'Driver is now online' : 'Driver is now offline'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // DELETE DRIVER
    // ============================================================
    public function destroy($driverId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can delete drivers.'
            ], 403);
        }

        try {
            $driver = Driver::with('user')->findOrFail($driverId);
            $user = $driver->user;

            if ($driver->vehicle) {
                $driver->vehicle->update(['driver_id' => null]);
            }
            
            $driver->delete();
            
            if ($user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Driver deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ============================================================
    // 5. ADMIN: DRIVER PAYMENTS
    // ============================================================

    public function payDriver(Request $request, $driverId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can pay drivers.'
            ], 403);
        }

        try {
            $driver = Driver::with('user')->findOrFail($driverId);
            $user = $driver->user;
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found for this driver.'
                ], 404);
            }
            
            $amount = $request->amount;
            $month = $request->month;
            $driverName = $request->driver_name ?? $user->name;
            
            if (!$amount || $amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment amount'
                ], 400);
            }
            
            $existingPayment = DriverPayment::where('driver_id', $driver->id)
                ->where('month', $month)
                ->where('paid_at', '>=', now()->startOfMonth())
                ->first();
            
            if ($existingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver has already been paid for ' . $month
                ], 400);
            }
            
            $earningsUpdated = Earning::where('driver_id', $driver->id)
                ->where('status', 'pending')
                ->update(['status' => 'paid']);
            
            $driver->available_balance -= $amount;
            $driver->total_withdrawn += $amount;
            $driver->last_payment_date = now();
            $driver->save();
            
            Log::info('✅ Driver payment processed', [
                'driver_id' => $driver->id,
                'user_id' => $user->id,
                'driver_name' => $driverName,
                'amount' => $amount,
                'month' => $month,
                'new_balance' => $driver->available_balance,
                'total_withdrawn' => $driver->total_withdrawn,
                'earnings_updated' => $earningsUpdated
            ]);
            
            $payment = DriverPayment::create([
                'driver_id' => $driver->id,
                'amount' => $amount,
                'month' => $month,
                'paid_by' => Auth::id(),
                'paid_at' => now(),
                'notes' => 'Payment for ' . $month . ' - ' . $driverName,
                'status' => 'completed'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => '✅ Payment of ' . number_format($amount, 0, ',', ' ') . ' F to ' . $driverName . ' recorded successfully!',
                'data' => [
                    'driver_id' => $driver->id,
                    'user_id' => $user->id,
                    'driver_name' => $driverName,
                    'amount' => $amount,
                    'month' => $month,
                    'payment_id' => $payment->id,
                    'paid_at' => now()->toDateTimeString(),
                    'earnings_updated' => $earningsUpdated,
                    'new_balance' => $driver->available_balance,
                    'total_withdrawn' => $driver->total_withdrawn
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Payment error: ' . $e->getMessage(), [
                'driver_id' => $driverId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paymentHistory($driverId)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can view payment history.'
            ], 403);
        }

        try {
            $driver = Driver::findOrFail($driverId);
            
            $payments = DriverPayment::where('driver_id', $driver->id)
                ->orderBy('paid_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'payments' => $payments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}