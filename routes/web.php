<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\EarningsController;  

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. TEST ROUTE
// ============================================================
Route::get('/test', function () {
    return '✅ Laravel is working! PHP Version: ' . PHP_VERSION;
});

// ============================================================
// 2. INDEX PAGE
// ============================================================
Route::get('/', function () {
    return view('index');
})->name('home');

// ============================================================
// 3. AUTHENTICATION ROUTES (Guest only)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ============================================================
// 4. ADMIN AUTHENTICATION ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

// ============================================================
// 5. LOGOUT
// ============================================================
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// ============================================================
// 6. PROFILE ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ============================================================
// 7. REPORTS ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

// ============================================================
// 8. TRACKING ROUTES (ALL tracking routes in one place)
// ============================================================
Route::middleware('auth')->group(function () {
    // Page view
    Route::get('/tracking/{order?}', [TrackingController::class, 'index'])->name('tracking');
    
    // Driver sends location update (uses TrackingController)
    Route::post('/tracking/{orderId}/location', [TrackingController::class, 'updateLocation'])
        ->name('tracking.update-location');
    
    // Get driver location for tracking (uses TrackingController)
    Route::get('/tracking/driver-location/{userId}', [TrackingController::class, 'getDriverLocation'])
        ->name('tracking.driver-location');
});

// ============================================================
// 9. NOTIFICATION ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});

// ============================================================
// 10. SETTINGS ROUTES
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update-notifications');
    Route::put('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.update-language');
    Route::put('/settings/driver', [SettingsController::class, 'updateDriverSettings'])->name('settings.update-driver');
    Route::put('/settings/admin', [SettingsController::class, 'updateAdminSettings'])->name('settings.update-admin');
});

// ============================================================
// 11. CUSTOMER ROUTES - Only role:customer
// ============================================================
Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customerDashboard'])->name('dashboard');
    Route::get('/create-order', [OrderController::class, 'create'])->name('create-order');
    Route::post('/orders', [OrderController::class, 'store'])->name('store-order');
    Route::get('/orders', [OrderController::class, 'customerOrders'])->name('orders');
    
    Route::post('/orders/{order}/confirm-price', [OrderController::class, 'confirmPrice'])->name('confirm-price');
    Route::get('/orders/{order}/pay', [OrderController::class, 'showPayment'])->name('pay');
    Route::post('/orders/{order}/pay', [OrderController::class, 'processPayment'])->name('process-payment');
});

// ============================================================
// 12. DRIVER ROUTES - Only role:driver
// ============================================================
Route::middleware(['role:driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [DriverController::class, 'orders'])->name('orders');
    Route::post('/toggle-status', [DriverController::class, 'toggleStatus'])->name('driver.toggle-status');
    
    Route::put('/orders/{order}/status', [DriverController::class, 'updateStatus'])->name('update-status');
    Route::post('/orders/{order}/status', [DriverController::class, 'updateStatus'])->name('update-status-post');
    
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals');
    Route::get('/withdraw', [WithdrawalController::class, 'create'])->name('withdraw');
    Route::post('/withdraw', [WithdrawalController::class, 'store'])->name('withdraw.store');
    Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show'])->name('withdrawal.show');
    
    Route::get('/earnings', [EarningsController::class, 'getHistory'])->name('earnings.history');
    Route::get('/earnings/summary', [EarningsController::class, 'getSummary'])->name('earnings.summary');
});

// ============================================================
// 13. ADMIN ROUTES - Only role:admin
// ============================================================
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    Route::get('/drivers', function () {
        $drivers = App\Models\Driver::with('user', 'vehicle')->get();
        return view('admin.drivers', compact('drivers'));
    })->name('drivers');
    
    Route::get('/drivers/{driver}/edit-data', [DriverController::class, 'getEditData'])->name('drivers.edit-data');
    Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
    Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
    Route::post('/drivers/{driver}/toggle-status', [DriverController::class, 'toggleStatusAdmin'])->name('drivers.toggle-status');
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');
    Route::post('/drivers/{driver}/pay', [DriverController::class, 'payDriver'])->name('drivers.pay');
    
    Route::get('/withdrawals', [WithdrawalController::class, 'adminIndex'])->name('withdrawals');
    Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'adminApprove'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/complete', [WithdrawalController::class, 'adminComplete'])->name('withdrawals.complete');
    Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'adminReject'])->name('withdrawals.reject');
    Route::get('/withdrawals/{id}/details', [WithdrawalController::class, 'adminGetDetails'])->name('withdrawals.details');
    
    Route::get('/customers', [UserController::class, 'customers'])->name('customers');
    Route::get('/customers/{user}/edit', [UserController::class, 'editCustomer'])->name('customers.edit');
    Route::put('/customers/{user}', [UserController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{user}', [UserController::class, 'destroyCustomer'])->name('customers.destroy');
    Route::post('/customers/{user}/toggle-status', [UserController::class, 'toggleCustomerStatus'])->name('customers.toggle-status');
    
    Route::get('/orders', [OrderController::class, 'adminOrders'])->name('orders');
    Route::put('/orders/{orderId}/price', [OrderController::class, 'setPrice'])->name('set-price');
    Route::get('/orders/{order}/assign', [OrderController::class, 'showAssignDriver'])->name('orders.assign');
    Route::post('/orders/{order}/assign', [OrderController::class, 'assignDriver'])->name('orders.assign.post');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    Route::get('/orders/{order}/drivers', [OrderController::class, 'getAllDrivers'])->name('orders.drivers');
    
    Route::get('/users', [UserController::class, 'pendingApprovals'])->name('users');
    // ✅ FIXED: Changed from PUT to POST
    Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
    
    Route::get('/reports/export', [ReportController::class, 'exportOrders'])->name('reports.export');
    
    Route::get('/earnings', [EarningsController::class, 'adminIndex'])->name('earnings');
});

// ============================================================
// 14. DASHBOARD REDIRECT
// ============================================================
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    
    if ($user->role === 'admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'driver') {
        return redirect('/driver/dashboard');
    } else {
        return redirect('/customer/dashboard');
    }
})->name('dashboard');

// ============================================================
// 15. TEST ROUTE (Loading check)
// ============================================================
Route::get('/test-route-loading', function () {
    return 'Routes are loading!';
});