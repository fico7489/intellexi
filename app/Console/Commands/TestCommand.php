<?php

namespace App\Console\Commands;

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
            'first_name' => 'test-first_name-'.rand(1, 1000000),
            'last_name' => '',
            'email' => '',
            'dob' => '2024-01-01',
            'role' => '',
        ]);
    }
}
