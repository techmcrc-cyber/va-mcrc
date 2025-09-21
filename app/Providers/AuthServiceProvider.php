<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\Retreat::class => \App\Policies\RetreatPolicy::class,
        \App\Models\Booking::class => \App\Policies\BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define a before callback that runs before all other authorization checks
        Gate::before(function (User $user, $ability) {
            // If user is a super admin, authorize all actions
            if ($user->role && $user->role->is_super_admin) {
                return true;
            }
        });

        // Define gates for all permissions
        $this->defineGates();
    }

    /**
     * Define gates for all permissions in the database
     */
    protected function defineGates(): void
    {
        try {
            // Get all permissions from the database
            $permissions = \App\Models\Permission::all();
            
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function (User $user) use ($permission) {
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
            // In case the permissions table doesn't exist yet (during migrations)
            return;
        }
    }
}
