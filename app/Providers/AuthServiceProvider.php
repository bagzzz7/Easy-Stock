<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Policies\UserPolicy;
use App\Policies\MedicinePolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Medicine::class => MedicinePolicy::class,
        Supplier::class => SupplierPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // User management gates
        Gate::define('view-users', function ($user) {
            return $user->role === 'administrator' || $user->role === 'staff';
        });

        Gate::define('create-user', function ($user) {
            return $user->role === 'administrator' || $user->role === 'staff';
        });

        Gate::define('update-user', function ($user, $targetUser) {
            if ($user->role === 'administrator') {
                return true;
            }
            if ($user->role === 'staff' && $targetUser->role === 'pharmacist') {
                return true;
            }
            return false;
        });

        Gate::define('delete-user', function ($user, $targetUser) {
            if ($user->role === 'administrator' && $user->id !== $targetUser->id) {
                return true;
            }
            if ($user->role === 'staff' && $targetUser->role === 'pharmacist') {
                return true;
            }
            return false;
        });

        // Medicine management gates
        Gate::define('manage-medicines', function ($user) {
            return true;
        });

        Gate::define('create-medicine', function ($user) {
            return true;
        });

        Gate::define('update-medicine', function ($user) {
            return true;
        });

        Gate::define('delete-medicine', function ($user) {
            return $user->role === 'administrator';
        });

        // Sales gates
        Gate::define('process-sales', function ($user) {
            return true;
        });

        Gate::define('view-sales', function ($user) {
            return true;
        });

        // Report gates
        Gate::define('view-reports', function ($user) {
            return true;
        });

        Gate::define('view-dashboard', function ($user) {
            return true;
        });

        // Add manage-users gate
        Gate::define('manage-users', function ($user) {
            return $user->role === 'administrator' || $user->role === 'staff';
        });
    }
}
