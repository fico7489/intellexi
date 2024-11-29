<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Second']);
        Role::create(['name' => 'Third']);
        Role::create(['name' => 'Fourth']);
        Role::create(['name' => 'Fifth']);
    }
}
