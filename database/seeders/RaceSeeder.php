<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\User;
use App\Models\UserType;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $faker = Factory::create();

            Race::create([
                'name' => $faker->name(),
                'distance' => Race::RACES[random_int(1, 3)],
            ]);
        }
    }
}
