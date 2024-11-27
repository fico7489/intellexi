<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserTypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RaceSeeder::class);
        $this->call(ApplicationSeeder::class);
    }
}
