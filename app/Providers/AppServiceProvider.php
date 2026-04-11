<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\User;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for MySQL older versions
        Schema::defaultStringLength(191);
        
        // Share categories with all views
        View::composer('*', function ($view) {
            try {
                $view->with('categories', Category::all());
            } catch (\Exception $e) {
                // Handle case when categories table doesn't exist yet (during migrations)
                $view->with('categories', collect([]));
            }
        });
        
        // Register Gates for authorization (alternative to policies)
        $this->registerGates();
    }
    
    /**
     * Register authorization gates.
     */
    protected function registerGates(): void
    {
        // User Management Gates
        Gate::define('view-users', function (User $user) {
            return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
        });
        
        Gate::define('create-user', function (User $user) {
            return $user->isAdministrator() || $user->isStaff();
        });
        
        Gate::define('update-user', function (User $user, User $targetUser) {
            if ($user->isAdministrator()) {
                return true;
            }
            
            if ($user->isStaff()) {
                // Staff can only update pharmacists
                return $targetUser->isPharmacist();
            }
            
            return false;
        });
        
        Gate::define('delete-user', function (User $user, User $targetUser) {
            return $user->isAdministrator() && $user->id !== $targetUser->id;
        });
        
        // Medicine Management Gates
        Gate::define('manage-medicines', function (User $user) {
            return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
        });
        
        Gate::define('create-medicine', function (User $user) {
            return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
        });
        
        Gate::define('update-medicine', function (User $user) {
            return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
        });
        
        Gate::define('delete-medicine', function (User $user) {
            return $user->isAdministrator() || $user->isStaff();
        });
        
        // Sales Management Gates
        Gate::define('process-sales', function (User $user) {
            return $user->isAdministrator() || $user->isStaff() || $user->isPharmacist();
        });
        
        Gate::define('view-sales', function (User $user) {
            return true; // All authenticated users can view sales
        });
        
        // Reports Gates
        Gate::define('view-reports', function (User $user) {
            return $user->isAdministrator() || $user->isStaff();
        });
        
        // Dashboard Gates
        Gate::define('view-dashboard', function (User $user) {
            return true; // All authenticated users can view dashboard
        });
    }
}