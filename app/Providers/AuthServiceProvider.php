<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [

    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('manager', function (User $user) {
            return $user->level === 'manager';
        });

        Gate::define('user', function (User $user) {
            return $user->level === 'user';
        });

        Gate::define('client', function (User $user) {
            return $user->level === 'client';
        });
    }
}
