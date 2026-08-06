<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // ============================================================
    // USER APPROVALS (Existing)
    // ============================================================

    public function pendingApprovals()
    {
        $pendingUsers = User::where('approval_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        $approvedUsers = User::where('approval_status', 'approved')
            ->whereNotNull('approved_at')
            ->orderBy('approved_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.users', compact('pendingUsers', 'approvedUsers'));
    }

    public function approve($id) 
    {
        $user = User::findOrFail($id);

        if ($user->isApproved()) {
            return redirect()->back()->with('error', 'User is already approved.');
        }

        $user->approve(Auth::id());

        return redirect()->route('admin.users')
            ->with('success', '✅ ' . $user->name . ' has been approved!');
    }

    public function reject(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isRejected()) {
            return redirect()->back()->with('error', 'User is already rejected.');
        }

        $user->reject(Auth::id(), $request->rejection_reason);

        return redirect()->route('admin.users')
            ->with('success', '❌ ' . $user->name . ' has been rejected.');
    }

    // ============================================================
    // ✅ CUSTOMER MANAGEMENT (NEW)
    // ============================================================

    /**
     * Display list of customers
     */
    public function customers()
    {
        $customers = User::where('role', 'customer')
            ->with('orders')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.customers', compact('customers'));
    }

    /**
     * Show form to edit customer
     */
    public function editCustomer($id)
    {
        $customer = User::with('orders')->findOrFail($id);
        
        // Check if user is a customer
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers')
                ->with('error', 'This user is not a customer.');
        }
        
        return view('admin.customers-edit', compact('customer'));
    }

    /**
     * Update customer
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_available' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.customers')
            ->with('success', '✅ Customer updated successfully!');
    }

    /**
     * Delete customer
     */
    public function destroyCustomer($id)
    {
        $customer = User::findOrFail($id);
        
        // Don't allow deleting admin users
        if ($customer->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users.'
            ], 403);
        }
        
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully!'
        ]);
    }

    /**
     * Toggle customer active status
     */
    public function toggleCustomerStatus($id)
    {
        $customer = User::findOrFail($id);
        
        if ($customer->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'This user is not a customer.'
            ], 403);
        }
        
        $customer->is_available = !$customer->is_available;
        $customer->save();

        return response()->json([
            'success' => true,
            'is_available' => $customer->is_available,
            'message' => $customer->is_available ? 'Customer is now active' : 'Customer is now inactive'
        ]);
    }

    // ============================================================
    // ✅ CUSTOMER EDIT VIEW (Alternative if you want a separate view)
    // ============================================================

    /**
     * Show customer details
     */
    public function showCustomer($id)
    {
        $customer = User::with('orders')->findOrFail($id);
        
        if ($customer->role !== 'customer') {
            return redirect()->route('admin.customers')
                ->with('error', 'This user is not a customer.');
        }
        
        return view('admin.customers-show', compact('customer')); 
    }
}