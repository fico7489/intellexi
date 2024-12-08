<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class Test2Command extends Command
{
    protected $signature = 'test2';

    public function handle()
    {
        $user = User::create([
            'first_name' => 'first_name-test'.rand(1, 1000000),
            'email' => 'test@test-email-'.rand(1, 1000000).'@gmail.com',
        ]);
        $user->delete();
    }
}
