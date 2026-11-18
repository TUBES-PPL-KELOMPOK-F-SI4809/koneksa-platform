<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('manage-business-profile', fn (User $user): bool => $user->role === 'umkm' && $user->account_status === 'enabled'
            && $user->email_verified_at !== null && ! $user->must_change_password
            && $user->deleted_at === null
        );

        Vite::prefetch(concurrency: 3);
    }
}
