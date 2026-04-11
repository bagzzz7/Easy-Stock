<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    

    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['sales' => function($q) {
            $q->latest()->limit(10);
        }]);

        // Calculate statistics
        $totalSales = $user->sales->count();
        $totalRevenue = $user->sales->sum('grand_total');
        $totalItems = $user->sales->sum(function($sale) {
            return $sale->items->sum('quantity');
        });

        return view('profile.show', compact('user', 'totalSales', 'totalRevenue', 'totalItems'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
        }

        // Upload new photo
        $fileName = time() . '_' . $user->id . '.' . $request->file('photo')->extension();
        $request->file('photo')->storeAs('profile-photos', $fileName, 'public');

        // Update user record
        $user->update(['profile_photo' => $fileName]);

        return redirect()->route('profile.show')
            ->with('success', 'Profile photo uploaded successfully.');
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile photo removed successfully.');
    }

    /**
     * Show activity log
     */
    public function activity()
    {
        $user = Auth::user();
        
        // Get recent sales activity
        $recentSales = $user->sales()->with('items.medicine')
            ->latest()
            ->paginate(20);

        return view('profile.activity', compact('user', 'recentSales'));
    }

    /**
     * Update notification preferences
     */
    public function notifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sales_alerts' => 'boolean',
            'stock_alerts' => 'boolean',
            'expiry_alerts' => 'boolean',
        ]);

        $user = Auth::user();
        
        // You can store these in a JSON column or create a settings table
        // For now, we'll use a simple approach
        $user->update([
            'settings' => json_encode([
                'email_notifications' => $request->boolean('email_notifications'),
                'sales_alerts' => $request->boolean('sales_alerts'),
                'stock_alerts' => $request->boolean('stock_alerts'),
                'expiry_alerts' => $request->boolean('expiry_alerts'),
            ])
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Notification preferences updated.');
    }
}