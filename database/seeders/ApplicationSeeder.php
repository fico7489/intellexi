<?php

namespace Database\Seeders;

use App\Models\Application;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $faker = Factory::create();

            Application::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'club' => $faker->name,
                'race_id' => random_int(1, 10),
                'user_id' => random_int(2, 10),
            ]);
        }
    }
}
