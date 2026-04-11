<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    

    private function canManageUser(User $targetUser)
    {
        $currentUser = auth()->user();

        if ($currentUser->isAdministrator()) {
            return true;
        }

        if ($currentUser->isStaff() && $targetUser->isPharmacist()) {
            return true;
        }

        return false;
    }

    public function index(Request $request)
    {
        $currentUser = auth()->user();

        $query = User::query();

        if ($currentUser->isAdministrator()) {
            $query->whereIn('role', [User::ROLE_STAFF, User::ROLE_PHARMACIST]);
        } else {
            $query->where('role', User::ROLE_PHARMACIST);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role != 'all' && $currentUser->isAdministrator()) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();

        $allowedRoles = $currentUser->isAdministrator()
            ? User::ROLE_STAFF . ',' . User::ROLE_PHARMACIST
            : User::ROLE_PHARMACIST;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:' . $allowedRoles,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        if (!$this->canManageUser($user)) {
            abort(403, 'You cannot view this user.');
        }

        $user->load(['sales' => function($q) {
            $q->latest()->limit(10);
        }]);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!$this->canManageUser($user)) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot edit this user.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!$this->canManageUser($user)) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot update this user.');
        }

        $currentUser = auth()->user();

        $allowedRoles = $currentUser->isAdministrator()
            ? User::ROLE_STAFF . ',' . User::ROLE_PHARMACIST
            : User::ROLE_PHARMACIST;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:' . $allowedRoles,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!$this->canManageUser($user)) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete this user.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($user->sales()->exists()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete user with sales history.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        if (!$this->canManageUser($user)) {
            return redirect()->back()
                ->with('error', 'You cannot modify this user.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }
}