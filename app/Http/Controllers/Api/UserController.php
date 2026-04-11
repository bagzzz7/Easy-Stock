<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        // Use the existing manage-users gate
        if (!Gate::allows('manage-users')) {
            return response()->json(['message' => 'Unauthorized to view users.'], 403);
        }

        $currentUser = $request->user();
        $query = User::query();
        
        // Staff can only see pharmacists
        if ($currentUser->isStaff()) {
            $query->where('role', User::ROLE_PHARMACIST);
        }
        
        // Administrator can see all users (staff and pharmacists)
        // No additional filter needed for admin
        
        // Filter by role (admin only)
        if ($currentUser->isAdministrator() && $request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        // Use the existing manage-users gate
        if (!Gate::allows('manage-users')) {
            return response()->json(['message' => 'Unauthorized to create users.'], 403);
        }
        
        $currentUser = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:staff,pharmacist',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Staff can only create pharmacists
        if ($currentUser->isStaff() && $request->role !== User::ROLE_PHARMACIST) {
            return response()->json(['message' => 'Staff can only create pharmacist accounts.'], 403);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'is_active' => true,
        ]);
        
        return response()->json([
            'user' => $user,
            'message' => 'User created successfully.'
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();
        
        // Check if user can view this specific user
        if ($currentUser->isStaff() && !$user->isPharmacist()) {
            return response()->json(['message' => 'Unauthorized to view this user.'], 403);
        }
        
        if (!$currentUser->isAdministrator() && $currentUser->id !== $user->id && !$currentUser->isStaff()) {
            return response()->json(['message' => 'Unauthorized to view this user.'], 403);
        }
        
        // Load relationships
        $user->loadCount('sales');
        $user->load('sales');
        
        // Calculate sales summary
        $user->sales_summary = [
            'total' => $user->sales()->count(),
            'revenue' => $user->sales()->sum('grand_total'),
            'items_sold' => $user->sales()->with('items')->get()->sum(function ($sale) {
                return $sale->items->sum('quantity');
            })
        ];
        
        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        // Use the existing manage-users gate
        if (!Gate::allows('manage-users')) {
            return response()->json(['message' => 'Unauthorized to update users.'], 403);
        }
        
        $currentUser = $request->user();
        
        // Staff cannot modify staff accounts
        if ($currentUser->isStaff() && $user->isStaff()) {
            return response()->json(['message' => 'You cannot modify staff accounts.'], 403);
        }
        
        // Staff can only modify pharmacists
        if ($currentUser->isStaff() && !$user->isPharmacist()) {
            return response()->json(['message' => 'You can only modify pharmacist accounts.'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role' => 'sometimes|in:staff,pharmacist',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Staff cannot change roles
        if ($currentUser->isStaff() && $request->has('role') && $request->role !== User::ROLE_PHARMACIST) {
            return response()->json(['message' => 'Staff can only set role to pharmacist.'], 403);
        }
        
        $updateData = $request->only(['name', 'phone', 'address', 'role', 'is_active']);
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully.'
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        // Use the existing manage-users gate
        if (!Gate::allows('manage-users')) {
            return response()->json(['message' => 'Unauthorized to delete users.'], 403);
        }
        
        $currentUser = $request->user();
        
        // Cannot delete yourself
        if ($user->id === $currentUser->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }
        
        // Staff cannot delete staff accounts
        if ($currentUser->isStaff() && $user->isStaff()) {
            return response()->json(['message' => 'You cannot delete staff accounts.'], 403);
        }
        
        // Staff can only delete pharmacists
        if ($currentUser->isStaff() && !$user->isPharmacist()) {
            return response()->json(['message' => 'You can only delete pharmacist accounts.'], 403);
        }
        
        // Check if user has sales records
        if ($user->sales()->exists()) {
            return response()->json([
                'message' => 'Cannot delete user with existing sales records. Consider deactivating instead.'
            ], 422);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        // Use the existing manage-users gate
        if (!Gate::allows('manage-users')) {
            return response()->json(['message' => 'Unauthorized to change user status.'], 403);
        }
        
        $currentUser = $request->user();
        
        // Staff cannot toggle staff accounts
        if ($currentUser->isStaff() && $user->isStaff()) {
            return response()->json(['message' => 'You cannot modify staff accounts.'], 403);
        }
        
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'user' => $user,
            'message' => "User {$status} successfully."
        ]);
    }
}