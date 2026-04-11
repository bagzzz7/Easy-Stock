<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Admin and Staff can view users, Pharmacists can only view pharmacists
        return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
    }

    /**
     * Determine if the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }
        
        if ($user->isStaff()) {
            return true;
        }
        
        if ($user->isPharmacist()) {
            // Pharmacists can only view other pharmacists
            return $model->isPharmacist();
        }
        
        return false;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        // Only admin and staff can create users
        return $user->isAdministrator() || $user->isStaff();
    }

    /**
     * Determine if the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // Only admin can update admin/staff, staff can update pharmacists
        if ($user->isAdministrator()) {
            return true;
        }
        
        if ($user->isStaff()) {
            // Staff can only update pharmacists, not other staff or admins
            return $model->isPharmacist();
        }
        
        return false;
    }

    /**
     * Determine if the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admin can delete users, and cannot delete themselves
        return $user->isAdministrator() && $user->id !== $model->id;
    }
}