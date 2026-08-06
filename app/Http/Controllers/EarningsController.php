<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Driver;
use App\Models\Earning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EarningsController extends Controller
{
    /**
     * ✅ Process earnings when an order is delivered
     * This is called from DriverController@updateStatus
     */
    public function processDeliveryEarnings(Order $order, Driver $driver)
    {
        try {
            DB::beginTransaction();
            
            // ✅ Calculate earning amount
            $earningAmount = $order->driver_earning ?? ($order->total_price * 0.5);
            
            // ✅ 1. Create earnings record
            $earning = Earning::create([
                'driver_id' => $driver->id,
                'order_id' => $order->id,
                'amount' => $earningAmount,
                'type' => 'delivery',
                'status' => 'pending',
                'description' => 'Earnings for delivery #' . $order->order_number,
                'earned_at' => now(),
            ]);
            
            // ✅ 2. Update driver's balance
            $driver->addEarnings($earningAmount);
            
            DB::commit();
            
            Log::info('✅ Earnings processed for delivery', [
                'driver_id' => $driver->id,
                'order_id' => $order->id,
                'amount' => $earningAmount,
                'earning_id' => $earning->id
            ]);
            
            return $earning;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error processing earnings: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ Get driver's earnings summary
     */
    public function getSummary(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'Only drivers can view earnings'
            ], 403);
        }
        
        $driver = $user->driver;
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }
        
        // ✅ Get today's earnings
        $todayEarnings = Earning::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('earned_at', today())
            ->sum('amount');
        
        // ✅ Get this week's earnings
        $weekEarnings = Earning::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereBetween('earned_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');
        
        // ✅ Get this month's earnings
        $monthEarnings = Earning::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereMonth('earned_at', now()->month)
            ->whereYear('earned_at', now()->year)
            ->sum('amount');
        
        return response()->json([
            'success' => true,
            'data' => [
                'available_balance' => $driver->available_balance,
                'total_earned' => $driver->total_earned,
                'today' => $todayEarnings,
                'this_week' => $weekEarnings,
                'this_month' => $monthEarnings,
            ]
        ]);
    }

    /**
     * ✅ Get driver's earnings history
     */
    public function getHistory(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'driver') {
            return response()->json([
                'success' => false,
                'message' => 'Only drivers can view earnings'
            ], 403);
        }
        
        $driver = $user->driver;
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver profile not found'
            ], 404);
        }
        
        $earnings = Earning::where('driver_id', $driver->id)
            ->with('order')
            ->orderBy('earned_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $earnings
        ]);
    }

    /**
     * ✅ Admin: Get all earnings (for reporting)
     */
    public function adminIndex(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can view all earnings.');
        }
        
        $earnings = Earning::with(['driver', 'driver.user', 'order'])
            ->orderBy('earned_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_earnings' => Earning::where('status', 'completed')->sum('amount'),
            'pending_earnings' => Earning::where('status', 'pending')->sum('amount'),
            'today_earnings' => Earning::where('status', 'completed')
                ->whereDate('earned_at', today())
                ->sum('amount'),
            'month_earnings' => Earning::where('status', 'completed')
                ->whereMonth('earned_at', now()->month)
                ->whereYear('earned_at', now()->year)
                ->sum('amount'),
        ];
        
        return view('admin.earnings', compact('earnings', 'stats'));
    }
}