<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            
            $activeDeliveries = Order::whereNotIn('status', ['delivered', 'cancelled'])->count();
            $onlineDrivers = Driver::where('is_available', true)->count();
            $totalDrivers = Driver::count();
            $idleDrivers = $totalDrivers - $onlineDrivers;
            
            $avgDeliveryTime = Order::where('status', 'delivered')
                ->whereNotNull('actual_delivery')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, actual_delivery)) as avg_time'))
                ->first()
                ->avg_time ?? 0;
            
            $todayRevenue = Order::where('status', 'delivered')
                ->whereDate('updated_at', today())
                ->sum('total_price');
            
            $recentDeliveries = Order::with(['customer', 'driver'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $todayOrders = Order::whereDate('created_at', today())->count();
            
            $pendingOrders = Order::where('status', Order::STATUS_PENDING)
                ->with(['customer', 'driver'])
                ->orderBy('created_at', 'asc')
                ->get();
            $pendingOrdersCount = $pendingOrders->count();
            
            $activeOrders = Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])
                ->with(['customer', 'driver'])
                ->orderBy('created_at', 'asc')
                ->get();
            $activeOrdersCount = $activeOrders->count();
            
            $monthlyRevenue = Order::where('status', 'delivered')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('total_price');
            
            $availableDrivers = Driver::with(['user', 'vehicle'])
                ->where('is_available', true)
                ->limit(10)
                ->get();
            
            $pendingDrivers = Driver::where('is_available', false)
                ->whereHas('user', function($query) {
                    $query->where('role', 'driver');
                })
                ->count();
            
            return view('dashboard.admin', compact(
                'activeDeliveries',
                'onlineDrivers',
                'idleDrivers',
                'totalDrivers',
                'avgDeliveryTime',
                'todayRevenue',
                'todayOrders',
                'pendingOrders',
                'pendingOrdersCount',
                'activeOrders',
                'activeOrdersCount',
                'monthlyRevenue',
                'recentDeliveries',
                'availableDrivers',
                'pendingDrivers'
            ));
        }
        elseif ($user->role === 'driver') {
            
            $driverId = $user->id;
            
            $activeOrder = Order::where('driver_id', $driverId)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->with('customer')
                ->first();
            
            $completedToday = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->whereDate('updated_at', today())
                ->count();
            
            $todayEarnings = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->whereDate('updated_at', today())
                ->sum('driver_earning');
            
            $totalCompleted = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->count();
            
            $totalEarnings = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->sum('driver_earning');
            
            $recentCompleted = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();
            
            $driverProfile = $user->driver;

            return view('dashboard.driver', compact(
                'activeOrder',
                'completedToday',
                'todayEarnings',
                'totalCompleted',
                'totalEarnings',
                'recentCompleted',
                'driverProfile'
            ));
        }
        else {
            
            $customerId = $user->id;
            
            // Orders waiting for price confirmation (Customer needs to confirm)
            $pendingPriceOrders = Order::where('customer_id', $customerId)
                ->where('status', Order::STATUS_PRICE_PENDING)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Orders confirmed waiting for payment
            $confirmedOrders = Order::where('customer_id', $customerId)
                ->where('status', Order::STATUS_PRICE_CONFIRMED)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $activeOrder = Order::where('customer_id', $customerId)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->with('driver')
                ->first();

            $totalOrders = Order::where('customer_id', $customerId)->count();
            
            $monthlyOrders = Order::where('customer_id', $customerId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $recentOrders = Order::where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $pendingPaymentCount = Order::where('customer_id', $customerId)
                ->whereIn('status', [Order::STATUS_PRICE_PENDING, Order::STATUS_PRICE_CONFIRMED])
                ->count();

            return view('dashboard.customer', compact(
                'activeOrder',
                'totalOrders',
                'monthlyOrders',
                'recentOrders',
                'pendingPriceOrders',
                'confirmedOrders',
                'pendingPaymentCount'
            ));
        }
    }

    public function adminDashboard()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access. You need to be an admin.');
        }
        
        // ============================================================
        // ORDER STATS
        // ============================================================
        
        $activeDeliveries = Order::whereNotIn('status', ['delivered', 'cancelled'])->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)
            ->with(['customer', 'driver'])
            ->orderBy('created_at', 'asc')
            ->get();
        $pendingOrdersCount = $pendingOrders->count();
        
        $activeOrders = Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])
            ->with(['customer', 'driver'])
            ->orderBy('created_at', 'asc')
            ->get();
        $activeOrdersCount = $activeOrders->count();
        
        $deliveredOrders = Order::where('status', 'delivered')->count();
        
        // ============================================================
        // REVENUE & EARNINGS
        // ============================================================
        
        // Today's revenue (total customer payments)
        $todayRevenue = Order::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('total_price');
        
        // Today's driver earnings
        $todayDriverEarnings = Order::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('driver_earning');
        
        // Today's platform earnings (platform fee)
        $todayPlatformEarnings = Order::where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('platform_fee');
        
        // Monthly revenue
        $monthlyRevenue = Order::where('status', 'delivered')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_price');
        
        // Monthly driver earnings
        $monthlyDriverEarnings = Order::where('status', 'delivered')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('driver_earning');
        
        // Monthly platform earnings
        $monthlyPlatformEarnings = Order::where('status', 'delivered')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('platform_fee');
        
        // ============================================================
        // DRIVER EARNINGS SUMMARY FOR THIS MONTH
        // ============================================================
        
        // Get driver earnings for this month using raw query
        $driverEarningsThisMonth = DB::table('orders')
            ->join('drivers', 'orders.driver_id', '=', 'drivers.user_id')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.updated_at', now()->month)
            ->whereYear('orders.updated_at', now()->year)
            ->select(
                'drivers.id as driver_id',
                'users.name as driver_name',
                DB::raw('COUNT(orders.id) as deliveries_count'),
                DB::raw('COALESCE(SUM(orders.distance_km), 0) as total_distance'),
                DB::raw('COALESCE(SUM(orders.weight_kg), 0) as total_weight'),
                DB::raw('COALESCE(SUM(orders.driver_earning), 0) as total_earned'),
                'drivers.last_payment_date'
            )
            ->groupBy('drivers.id', 'users.name', 'drivers.last_payment_date')
            ->having('deliveries_count', '>', 0)
            ->get()
            ->map(function($item) {
                // Add status field based on last_payment_date
                $item->status = $item->last_payment_date ? 'paid' : 'pending';
                // Add driver object for compatibility with blade
                $item->driver = (object) [
                    'id' => $item->driver_id,
                    'name' => $item->driver_name,
                    'last_payment_date' => $item->last_payment_date,
                ];
                return $item;
            });
        
        $totalDriverEarningsThisMonth = $driverEarningsThisMonth->sum('total_earned');
        
        $totalPlatformEarningsThisMonth = Order::where('status', 'delivered')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('platform_fee');
        
        // ============================================================
        // DRIVER STATS
        // ============================================================
        
        $onlineDrivers = Driver::where('is_available', true)->count();
        $totalDrivers = Driver::count();
        $idleDrivers = $totalDrivers - $onlineDrivers;
        
        $availableDrivers = Driver::with(['user', 'vehicle'])
            ->where('is_available', true)
            ->limit(10)
            ->get();
        
        $pendingDrivers = Driver::where('is_available', false)
            ->whereHas('user', function($query) {
                $query->where('role', 'driver');
            })
            ->count();
        
        // ============================================================
        // PLATFORM SUMMARY
        // ============================================================
        
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrdersCount = Order::count();
        
        // ============================================================
        // RECENT DELIVERIES
        // ============================================================
        
        $recentDeliveries = Order::with(['customer', 'driver'])
            ->where('status', 'delivered')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        // ============================================================
        // AVERAGE DELIVERY TIME
        // ============================================================
        
        $avgDeliveryTime = Order::where('status', 'delivered')
            ->whereNotNull('actual_delivery')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, actual_delivery)) as avg_time'))
            ->first()
            ->avg_time ?? 0;
        
        return view('dashboard.admin', compact(
            'activeDeliveries',
            'todayOrders',
            'pendingOrders',
            'pendingOrdersCount',
            'activeOrders',
            'activeOrdersCount',
            'deliveredOrders',
            'todayRevenue',
            'todayDriverEarnings',
            'todayPlatformEarnings',
            'monthlyRevenue',
            'monthlyDriverEarnings',
            'monthlyPlatformEarnings',
            'driverEarningsThisMonth',
            'totalDriverEarningsThisMonth',
            'totalPlatformEarningsThisMonth',
            'onlineDrivers',
            'totalDrivers',
            'idleDrivers',
            'availableDrivers',
            'pendingDrivers',
            'totalCustomers',
            'totalOrdersCount',
            'recentDeliveries',
            'avgDeliveryTime'
        ));
    }

    public function driverDashboard()
    {
        if (Auth::user()->role !== 'driver') {
            abort(403, 'Unauthorized access. You need to be a driver.');
        }
        
        $driverId = Auth::id();
        
        $activeOrder = Order::where('driver_id', $driverId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with('customer')
            ->first();
        
        $completedToday = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();
        
        $todayEarnings = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('driver_earning');
        
        $totalCompleted = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->count();
        
        $totalEarnings = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->sum('driver_earning');
        
        $recentCompleted = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        $driverProfile = Auth::user()->driver;

        return view('dashboard.driver', compact(
            'activeOrder',
            'completedToday',
            'todayEarnings',
            'totalCompleted',
            'totalEarnings',
            'recentCompleted',
            'driverProfile'
        ));
    }

    public function customerDashboard()
    {
        if (Auth::user()->role !== 'customer') {
            abort(403, 'Unauthorized access. You need to be a customer.');
        }
        
        $customerId = Auth::id();
        
        // Orders waiting for price confirmation
        $pendingPriceOrders = Order::where('customer_id', $customerId)
            ->where('status', Order::STATUS_PRICE_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Orders confirmed waiting for payment
        $confirmedOrders = Order::where('customer_id', $customerId)
            ->where('status', Order::STATUS_PRICE_CONFIRMED)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $activeOrder = Order::where('customer_id', $customerId)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with('driver')
            ->first();

        $totalOrders = Order::where('customer_id', $customerId)->count();
        
        $monthlyOrders = Order::where('customer_id', $customerId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $recentOrders = Order::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $pendingPaymentCount = Order::where('customer_id', $customerId)
            ->whereIn('status', [Order::STATUS_PRICE_PENDING, Order::STATUS_PRICE_CONFIRMED])
            ->count();

        return view('dashboard.customer', compact(
            'activeOrder',
            'totalOrders',
            'monthlyOrders',
            'recentOrders',
            'pendingPriceOrders',
            'confirmedOrders',
            'pendingPaymentCount'
        ));
    }
}