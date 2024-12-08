<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function handle()
    {
        /*$role = Role::create([
            'name' => 'test-role-'.rand(1, 1000000),
        ]);*/

        $user = User::create([
            'email' => 'test@test-email-'.rand(1, 1000000).'@gmail.com',
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'club' => 'test',
        ]);
    }
}
