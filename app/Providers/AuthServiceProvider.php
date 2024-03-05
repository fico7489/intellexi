<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];

    public function boot(): void
    {
        Gate::define('manage-race', function (User $user) {
            return User::ROLE_ADMINISTRATOR === $user->role;
        });

        Gate::define('manage-application', function (User $user, Application $application) {
            return
                User::ROLE_ADMINISTRATOR === $user->role
                or (User::ROLE_APPLICANT === $user->role and $application->user_id == $user->id);
        });
    }
}
