<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super Admin mendapatkan semua akses tanpa perlu Spatie role
        Gate::before(function ($user, $ability) {
            if ($user->username === 'admin'
                || in_array($user->is_admin, ['admin', 'superadmin', 'Super Admin'])
                || $user->name === 'Super Admin') {
                return true;
            }
        });
    }
}
