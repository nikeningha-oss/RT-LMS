<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show reports based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // If admin, show admin reports
        if ($user->role === 'admin') {
            return $this->adminReports();
        }

        // If driver, show driver reports
        if ($user->role === 'driver') {
            return $this->driverReports();
        }

        // If customer, show customer reports
        return $this->customerReports();
    }

    /**
     * Customer Reports - Personal delivery statistics
     */
    public function customerReports()
    {
        $userId = Auth::id();

        // Total orders
        $totalOrders = Order::where('customer_id', $userId)->count();

        // Orders by status
        $pendingOrders = Order::where('customer_id', $userId)->where('status', 'pending')->count();
        $deliveredOrders = Order::where('customer_id', $userId)->where('status', 'delivered')->count();
        $cancelledOrders = Order::where('customer_id', $userId)->where('status', 'cancelled')->count();
        $inTransitOrders = Order::where('customer_id', $userId)->where('status', 'in_transit')->count();

        // Total spent
        $totalSpent = Order::where('customer_id', $userId)->where('status', 'delivered')->sum('total_price');

        // Monthly orders (last 6 months)
        $monthlyOrders = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyOrders[] = [
                'month' => $month->format('M'),
                'count' => Order::where('customer_id', $userId)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
                'spent' => Order::where('customer_id', $userId)
                    ->where('status', 'delivered')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('total_price'),
            ];
        }

        // Recent orders
        $recentOrders = Order::where('customer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Average delivery time for your orders
        $avgDeliveryTime = Order::where('customer_id', $userId)
            ->where('status', 'delivered')
            ->whereNotNull('actual_delivery')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, actual_delivery)) as avg_time'))
            ->first()
            ->avg_time ?? 0;

        return view('reports.customer', compact(
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'cancelledOrders',
            'inTransitOrders',
            'totalSpent',
            'monthlyOrders',
            'recentOrders',
            'avgDeliveryTime'
        ));
    }

    /**
     * Driver Reports - Personal delivery performance
     */
    public function driverReports()
    {
        $userId = Auth::id();

        // Total assigned orders
        $totalOrders = Order::where('driver_id', $userId)->count();

        // Orders by status
        $deliveredOrders = Order::where('driver_id', $userId)->where('status', 'delivered')->count();
        $inTransitOrders = Order::where('driver_id', $userId)->where('status', 'in_transit')->count();
        $pendingOrders = Order::where('driver_id', $userId)->where('status', 'pending')->count();

        // Total earnings
        $totalEarnings = Order::where('driver_id', $userId)
            ->where('status', 'delivered')
            ->sum('total_price');

        // Weekly orders
        $weekOrders = Order::where('driver_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Monthly orders
        $monthOrders = Order::where('driver_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Recent deliveries
        $recentOrders = Order::where('driver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.driver', compact(
            'totalOrders',
            'deliveredOrders',
            'inTransitOrders',
            'pendingOrders',
            'totalEarnings',
            'weekOrders',
            'monthOrders',
            'recentOrders'
        ));
    }

    /**
     * Admin Reports - System-wide analytics
     */
    public function adminReports()
    {
        // Total orders
        $totalOrders = Order::count();

        // Orders by status
        $pendingOrders = Order::where('status', 'pending')->count();
        $assignedOrders = Order::where('status', 'assigned')->count();
        $inTransitOrders = Order::where('status', 'in_transit')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Today's orders
        $todayOrders = Order::whereDate('created_at', today())->count();

        // This week's orders
        $weekOrders = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

        // This month's orders
        $monthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Total revenue
        $totalRevenue = Order::where('status', 'delivered')->sum('total_price');

        // Today's revenue
        $todayRevenue = Order::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('total_price');

        // Total drivers
        $totalDrivers = User::where('role', 'driver')->count();

        // Total customers
        $totalCustomers = User::where('role', 'customer')->count();

        // Active drivers (have at least one delivery this month)
        $activeDrivers = User::where('role', 'driver')
            ->whereHas('assignedOrders', function ($query) {
                $query->whereMonth('orders.created_at', now()->month)
                    ->whereYear('orders.created_at', now()->year);
            })
            ->count();

        // Top drivers
        $topDrivers = User::where('role', 'driver')
            ->withCount(['assignedOrders' => function ($query) {
                $query->where('status', 'delivered');
            }])
            ->orderBy('assigned_orders_count', 'desc')
            ->limit(5)
            ->get();

        // Daily orders (last 7 days)
        $dailyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyOrders[] = [
                'date' => $date->format('M d'),
                'count' => Order::whereDate('orders.created_at', $date)->count(),
                'revenue' => Order::where('status', 'delivered')
                    ->whereDate('orders.updated_at', $date)
                    ->sum('total_price'),
            ];
        }

        // Average delivery time
        $avgDeliveryTime = Order::where('status', 'delivered')
            ->whereNotNull('actual_delivery')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, orders.created_at, orders.actual_delivery)) as avg_time'))
            ->first()
            ->avg_time ?? 0;

        // On-time delivery rate
        $onTimeDeliveries = Order::where('status', 'delivered')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, actual_delivery) <= 24')
            ->count();
        $onTimeRate = $deliveredOrders > 0 ? round(($onTimeDeliveries / $deliveredOrders) * 100, 1) : 0;

        // Recent orders
        $recentOrders = Order::with(['customer', 'driver'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('reports.admin', compact(
            'totalOrders',
            'pendingOrders',
            'assignedOrders',
            'inTransitOrders',
            'deliveredOrders',
            'cancelledOrders',
            'todayOrders',
            'weekOrders',
            'monthOrders',
            'totalRevenue',
            'todayRevenue',
            'totalDrivers',
            'totalCustomers',
            'activeDrivers',
            'topDrivers',
            'dailyOrders',
            'avgDeliveryTime',
            'onTimeRate',
            'recentOrders'
        ));
    }

    /**
     * Export orders report (CSV)
     */
    public function exportOrders()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $orders = Order::with(['customer', 'driver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order Number', 'Customer', 'Driver', 'Pickup Address',
                'Delivery Address', 'Status', 'Price (FCFA)', 'Created At', 'Delivered At'
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->customer->name ?? 'N/A',
                    $order->driver->name ?? 'Unassigned',
                    $order->pickup_address,
                    $order->delivery_address,
                    ucfirst($order->status),
                    number_format($order->total_price, 0, ',', ' '),
                    $order->created_at->format('Y-m-d H:i'),
                    $order->actual_delivery ? $order->actual_delivery->format('Y-m-d H:i') : 'N/A'
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}