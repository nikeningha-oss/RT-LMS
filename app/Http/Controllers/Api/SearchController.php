<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Search for orders, drivers, and customers
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            // Get the search query from the request
            $query = $request->query('q', '');
            
            // If query is too short, return empty results
            if (strlen($query) < 2) {
                return response()->json([]);
            }

            $results = [];
            $user = Auth::user();

            // ============================================================
            // 1. SEARCH ORDERS (All users can search their accessible orders)
            // ============================================================
            $orders = Order::where(function ($q) use ($query) {
                    $q->where('order_number', 'LIKE', "%{$query}%")
                      ->orWhere('pickup_address', 'LIKE', "%{$query}%")
                      ->orWhere('delivery_address', 'LIKE', "%{$query}%");
                })
                ->when($user->role === 'customer', function ($q) use ($user) {
                    // Customers can only see their own orders
                    return $q->where('customer_id', $user->id);
                })
                ->when($user->role === 'driver', function ($q) use ($user) {
                    // Drivers can only see orders assigned to them
                    return $q->where('driver_id', $user->id);
                })
                ->limit(10)
                ->get();

            foreach ($orders as $order) {
                $results[] = [
                    'id' => $order->id,
                    'title' => '📦 Order ' . $order->order_number,
                    'description' => 'Pickup: ' . $order->pickup_address . ' → ' . $order->delivery_address,
                    'type' => 'order',
                    'url' => route('tracking', $order->id),
                    'status' => $order->status,
                ];
            }

            // ============================================================
            // 2. SEARCH DRIVERS (Admin only)
            // ============================================================
            if ($user->role === 'admin') {
                $drivers = User::where('role', 'driver')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get();

                foreach ($drivers as $driver) {
                    $results[] = [
                        'id' => $driver->id,
                        'title' => '🚚 ' . $driver->name,
                        'description' => 'Driver · ' . $driver->email,
                        'type' => 'driver',
                        'url' => route('admin.drivers'),
                        'status' => null,
                    ];
                }

                // ============================================================
                // 3. SEARCH CUSTOMERS (Admin only)
                // ============================================================
                $customers = User::where('role', 'customer')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get();

                foreach ($customers as $customer) {
                    $results[] = [
                        'id' => $customer->id,
                        'title' => '👤 ' . $customer->name,
                        'description' => 'Customer · ' . $customer->email,
                        'type' => 'customer',
                        'url' => route('admin.orders'),
                        'status' => null,
                    ];
                }
            }

            // Return results as JSON
            return response()->json($results);
            
        } catch (\Exception $e) {
            // Return error details for debugging
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
}