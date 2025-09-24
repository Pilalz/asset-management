<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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

        Gate::define('is-owner', function (User $user) {
            return $user->role === 'owner';
        });

        Gate::define('is-asset-management', function (User $user) {
            return $user->role === 'Asset Management';
        });

        Gate::define('is-user', function (User $user) {
            return $user->role === 'User';
        });

        // Gate untuk memeriksa apakah user adalah Admin
        // Gate::define('access-admin-dashboard', function ($user) {
        //     // Ganti 'admin@example.com' dengan logika Anda
        //     return $user->email === 'admin@example.com'; 
        // });
    }
}
