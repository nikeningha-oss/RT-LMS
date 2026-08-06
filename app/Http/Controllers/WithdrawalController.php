<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    // ============================================================
    // DRIVER METHODS
    // ============================================================

    public function index()
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if (!$driver) {
            return redirect()->back()->with('error', 'Driver profile not found.');
        }

        $withdrawals = WithdrawalRequest::where('driver_id', $driver->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('driver.withdrawals', compact('withdrawals', 'driver'));
    }

    public function create()
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if (!$driver) {
            return redirect()->back()->with('error', 'Driver profile not found.');
        }

        return view('driver.withdraw', compact('driver'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if (!$driver) {
            return redirect()->back()->with('error', 'Driver profile not found.');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mtn,orange,bank',
            'account_details' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $amount = $request->amount;

        if (!$driver->hasSufficientBalance($amount)) {
            return redirect()->back()
                ->with('error', 'Insufficient balance. Your available balance is ' . $driver->formatted_available_balance);
        }

        $fee = WithdrawalRequest::calculateFee($amount);
        $netAmount = WithdrawalRequest::calculateNetAmount($amount);

        try {
            DB::beginTransaction();

            $withdrawal = WithdrawalRequest::create([
                'driver_id' => $driver->id,
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $netAmount,
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
                'status' => WithdrawalRequest::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            $driver->available_balance -= $amount;
            $driver->total_withdrawn += $amount;
            $driver->save();

            DB::commit();

            return redirect()->route('driver.withdrawals')
                ->with('success', '✅ Withdrawal request submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error submitting withdrawal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $driver = $user->driver;
        
        if (!$driver) {
            return redirect()->back()->with('error', 'Driver profile not found.');
        }

        $withdrawal = WithdrawalRequest::where('driver_id', $driver->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('driver.withdrawal-details', compact('withdrawal'));
    }

    // ============================================================
    // ADMIN METHODS - ✅ FIXED FOR AJAX
    // ============================================================

    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $pendingWithdrawals = WithdrawalRequest::with(['driver', 'driver.user'])
            ->where('status', WithdrawalRequest::STATUS_PENDING)
            ->orderBy('requested_at', 'asc')
            ->get();

        $approvedWithdrawals = WithdrawalRequest::with(['driver', 'driver.user'])
            ->where('status', WithdrawalRequest::STATUS_APPROVED)
            ->orderBy('processed_at', 'desc')
            ->get();

        $completedWithdrawals = WithdrawalRequest::with(['driver', 'driver.user'])
            ->where('status', WithdrawalRequest::STATUS_COMPLETED)
            ->orderBy('processed_at', 'desc')
            ->limit(20)
            ->get();

        $rejectedWithdrawals = WithdrawalRequest::with(['driver', 'driver.user'])
            ->where('status', WithdrawalRequest::STATUS_REJECTED)
            ->orderBy('processed_at', 'desc')
            ->limit(20)
            ->get();

        $totalPendingAmount = $pendingWithdrawals->sum('amount');
        $totalPendingCount = $pendingWithdrawals->count();
        $totalWithdrawnThisMonth = WithdrawalRequest::where('status', WithdrawalRequest::STATUS_COMPLETED)
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('net_amount');

        return view('admin.withdrawals', compact(
            'pendingWithdrawals',
            'approvedWithdrawals',
            'completedWithdrawals',
            'rejectedWithdrawals',
            'totalPendingAmount',
            'totalPendingCount',
            'totalWithdrawnThisMonth'
        ));
    }

    /**
     * ✅ FIXED: Admin approve withdrawal - Returns JSON for AJAX
     */
    public function adminApprove(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        try {
            $withdrawal = WithdrawalRequest::with('driver')->findOrFail($id);

            if (!$withdrawal->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This withdrawal request has already been processed.'
                ], 400);
            }

            $withdrawal->approve(Auth::id(), $request->admin_note);

            Log::info('✅ Withdrawal approved', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Withdrawal request approved successfully! Amount: ' . $withdrawal->formatted_amount
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FIXED: Admin complete withdrawal - Returns JSON for AJAX
     */
    public function adminComplete(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        try {
            $withdrawal = WithdrawalRequest::with('driver')->findOrFail($id);

            if (!$withdrawal->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved withdrawals can be marked as completed.'
                ], 400);
            }

            $withdrawal->complete(Auth::id(), $request->admin_note);

            Log::info('💰 Withdrawal completed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Withdrawal marked as completed!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ FIXED: Admin reject withdrawal - Returns JSON for AJAX
     */
    public function adminReject(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        try {
            $withdrawal = WithdrawalRequest::with('driver')->findOrFail($id);

            if (!$withdrawal->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This withdrawal request has already been processed.'
                ], 400);
            }

            $withdrawal->reject(Auth::id(), $request->reason);

            Log::info('❌ Withdrawal rejected', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => '❌ Withdrawal request rejected. Amount refunded to driver.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting withdrawal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminGetDetails($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $withdrawal = WithdrawalRequest::with(['driver', 'driver.user'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'withdrawal' => [
                    'id' => $withdrawal->id,
                    'driver_name' => $withdrawal->driver->name,
                    'driver_email' => $withdrawal->driver->user->email ?? 'N/A',
                    'driver_phone' => $withdrawal->driver->phone,
                    'amount' => number_format($withdrawal->amount, 0, ',', ' ') . ' F',
                    'fee' => number_format($withdrawal->fee, 0, ',', ' ') . ' F',
                    'net_amount' => number_format($withdrawal->net_amount, 0, ',', ' ') . ' F',
                    'payment_method' => $withdrawal->payment_method_label,
                    'account_details' => $withdrawal->account_details,
                    'status' => $withdrawal->status_label,
                    'requested_at' => $withdrawal->requested_at ? $withdrawal->requested_at->format('d M Y, h:i A') : 'N/A',
                    'available_balance' => number_format($withdrawal->driver->available_balance ?? 0, 0, ',', ' ') . ' F',
                    'total_earned' => number_format($withdrawal->driver->total_earned ?? 0, 0, ',', ' ') . ' F',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}