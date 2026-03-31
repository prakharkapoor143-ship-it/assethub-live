<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::define('manage-inventory', fn ($user) => in_array($user->role, ['admin', 'manager'], true));
        Gate::define('admin-only', fn ($user) => $user->role === 'admin');
    }
}
