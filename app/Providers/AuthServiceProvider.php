<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Gate::define('manage-race', function (User $user) {
            return $user->role === 'Administrator';
        });

        Gate::define('manage-application', function (User $user, Application $application) {
            return
                $user->role === 'Administrator'
                or
                ($user->role === 'Applicant' and $application->user_id == $user->id);
        });
    }
}