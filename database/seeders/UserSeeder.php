<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        //Administrator
        User::create([
            'first_name' => 'Administrator',
            'last_name' => 'Administrator',
            'email' => 'administrator@example.com',
            'dob' => '1990-01-01',
            'role' => User::ROLE_ADMINISTRATOR,
            'user_type_id' => 1,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $faker = Factory::create();

            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'dob' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                'role' => User::ROLE_ADMINISTRATOR,
                'user_type_id' => random_int(1, 3),
            ]);
        }
    }
}
