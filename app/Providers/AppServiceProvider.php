<?php

namespace App\Providers;

use App\Console\Commands\TestCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->when(TestCommand::class)->needs('$test')->give('12344444');
    }

    public function boot(): void
    {
    }
}
