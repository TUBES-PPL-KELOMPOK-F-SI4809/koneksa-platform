<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Policies\BusinessPolicy;
use App\Policies\PartnerProfilePolicy;
use App\Policies\UserPolicy;
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

        Gate::policy(Business::class, BusinessPolicy::class);
        Gate::policy(PartnerProfile::class, PartnerProfilePolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Gate otorisasi peran & akses modul
        Gate::define('access-admin', fn (User $user) => $user->isAdmin() && $user->isEnabled());
        Gate::define('access-pemda', fn (User $user) => $user->isPemda() && $user->isEnabled());
        Gate::define('access-umkm', fn (User $user) => $user->isUmkm() && $user->isEnabled());
        Gate::define('access-mitra', fn (User $user) => $user->isMitra() && $user->isEnabled());
    }
}
