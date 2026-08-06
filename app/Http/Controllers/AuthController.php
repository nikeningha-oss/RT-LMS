<?php

/*
|============================================================
| AUTH CONTROLLER
|============================================================
| This controller handles everything related to authentication:
| - Showing login/register pages
| - Logging users in
| - Registering new users
| - Logging users out
| - Admin login
*/

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // ============================================================
    // 1. SHOW LOGIN PAGE
    // ============================================================
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // ============================================================
    // 2. HANDLE LOGIN FORM SUBMISSION - FIXED
    // ============================================================
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user has a role
            if (empty($user->role)) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', '❌ Your account does not have a role assigned.');
            }

            // ✅ FIXED: Check approval status - More lenient for existing users
            if (Schema::hasColumn('users', 'approval_status')) {
                // If approval_status is NULL or empty, treat as 'approved' for backward compatibility
                $status = $user->approval_status ?? 'approved';
                
                if ($status === 'pending') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', '⏳ Your account is pending admin approval. Please wait for admin to approve your account.');
                }

                if ($status === 'rejected') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', '❌ Your account has been rejected. Please contact support for more information.');
                }

                // Only admins bypass approval check
                if ($user->role !== 'admin' && $status !== 'approved' && $status !== 'active') {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', '❌ Your account is not approved. Please contact support.');
                }
            }

            $request->session()->regenerate();
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')
                    ->with('success', 'Welcome back, Admin!');
            } 
            
            elseif ($user->role === 'driver') {
                // Check if driver profile exists
                $driver = Driver::where('user_id', $user->id)->first();
                
                if (!$driver) {
                    // Create driver profile
                    try {
                        $driver = Driver::create([
                            'user_id' => $user->id,
                            'license_number' => $user->license_number ?? 'PENDING-' . $user->id,
                            'phone' => $user->phone ?? '',
                            'is_available' => true,
                        ]);
                        Log::info('✅ Created driver profile for: ' . $user->email);
                    } catch (\Exception $e) {
                        Log::error('❌ Failed to create driver profile: ' . $e->getMessage());
                    }
                } else {
                    Log::info('✅ Driver profile already exists for: ' . $user->email);
                }

                return redirect('/driver/dashboard')
                    ->with('success', 'Welcome back, Driver!');
            } 
            
            elseif ($user->role === 'customer') {
                return redirect('/customer/dashboard')
                    ->with('success', 'Welcome back!');
            } 
            
            else {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', '❌ Invalid user role. Please contact support.');
            }
        }

        return redirect()->back()
            ->withErrors(['email' => '❌ These credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    // ============================================================
    // 3. SHOW REGISTER PAGE
    // ============================================================
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    // ============================================================
    // 4. HANDLE REGISTRATION FORM SUBMISSION - FIXED
    // ============================================================
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,driver',
            'terms' => 'accepted',
        ]);

        // ✅ NEW: Additional validation for driver
        if ($request->role === 'driver') {
            $validator->setRules(array_merge($validator->getRules(), [
                'license_number' => 'required|string|max:50|unique:drivers,license_number',
            ]));
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        // ✅ FIXED: Set approval_status based on role
        if (Schema::hasColumn('users', 'approval_status')) {
            // Customers are auto-approved, Drivers need admin approval
            $userData['approval_status'] = $request->role === 'customer' ? 'approved' : 'pending';
        }

        $user = User::create($userData);

        // ✅ FIXED: Create driver profile with license number
        if ($user->role === 'driver') {
            Driver::create([
                'user_id' => $user->id,
                'license_number' => $request->license_number,
                'phone' => $request->phone ?? '',
                'is_available' => false,
            ]);
        }

        // ✅ FIXED: Auto-login only for customers
        if ($user->role === 'customer') {
            Auth::login($user);
            return redirect('/customer/dashboard')
                ->with('success', '✅ Account created successfully! Welcome to Tracklane.');
        }

        // ✅ FIXED: For drivers, show pending message
        if ($user->role === 'driver') {
            return redirect()->route('login')
                ->with('status', '✅ Account created! Your driver profile is pending admin approval. You will be notified once approved.');
        }

        return redirect()->route('login')
            ->with('status', '✅ Account created successfully! Please login.');
    }

    // ============================================================
    // 5. HANDLE LOGOUT
    // ============================================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->with('status', '👋 You have been logged out successfully.');
    }

    // ============================================================
    // 6. ADMIN LOGIN - SHOW PAGE
    // ============================================================
    public function showAdminLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.admin-login');
    }

    // ============================================================
    // 7. ADMIN LOGIN - PROCESS
    // ============================================================
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect('/admin/dashboard')
                    ->with('success', 'Welcome back, Admin!');
            }
            
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid admin credentials.'])
            ->withInput($request->except('password'));
    }
}