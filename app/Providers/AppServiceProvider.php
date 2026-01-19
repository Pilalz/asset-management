<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Company;
use App\Policies\CompanyPolicy;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::define('is-dev', function (User $user) {
            return $user->akses === 'Dev';
        });

        Gate::define('is-owner', function (User $user) {
            return $user->role === 'Owner';
        });

        Gate::define('is-admin', function (User $user) {
            // Check role is not empty before validating
            // User.role depends on last_active_company_id being set
            $role = $user->role;
            return !empty($role) && in_array($role, ['Owner', 'Asset Management']);
        });

        Gate::define('is-form-maker', function (User $user) {
            return in_array($user->role, ['Owner', 'Asset Management']);
        });

        // Gate untuk memeriksa apakah user adalah Admin
        // Gate::define('access-admin-dashboard', function ($user) {
        //     // Ganti 'admin@example.com' dengan logika Anda
        //     return $user->email === 'admin@example.com'; 
        // });

        Gate::policy(Company::class, CompanyPolicy::class);
    }
}
