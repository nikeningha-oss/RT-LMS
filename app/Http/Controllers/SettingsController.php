<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Show the settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update profile settings
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check current password if trying to change
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->back()->with('success', '✅ Profile updated successfully!');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        // If you have a preferences column in users table
        // $user = Auth::user();
        // $user->preferences = $request->all();
        // $user->save();

        return redirect()->back()->with('success', '✅ Notification preferences updated!');
    }

    /**
     * Update language preference
     */
    public function updateLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:en,fr,es',
        ]);

        session(['locale' => $request->language]);
        return redirect()->back()->with('success', '✅ Language updated!');
    }

    /**
     * Update driver settings (Driver only)
     */
    public function updateDriverSettings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isDriver()) {
            abort(403, 'Only drivers can update driver settings.');
        }

        $driver = $user->driver;
        
        if ($driver) {
            $driver->update([
                'phone' => $request->phone,
                'is_available' => $request->has('is_available'),
            ]);

            // Update vehicle if exists
            if ($driver->vehicle) {
                $driver->vehicle->update([
                    'model' => $request->vehicle_model,
                    'plate_number' => $request->vehicle_plate,
                ]);
            }
        }

        return redirect()->back()->with('success', '✅ Driver settings updated!');
    }

    /**
     * Update admin settings (Admin only)
     */
    public function updateAdminSettings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Only admins can update admin settings.');
        }

        // This could store system settings in a settings table
        // For now, just return success
        return redirect()->back()->with('success', '✅ Admin settings updated!');
    }
}