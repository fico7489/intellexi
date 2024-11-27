<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\User;
use App\Models\UserType;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    public function run()
    {
        //Administrator
        UserType::create(['name' => 'First']);
        UserType::create(['name' => 'Second']);
        UserType::create(['name' => 'Third']);
    }
}
