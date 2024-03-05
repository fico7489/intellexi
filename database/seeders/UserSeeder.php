<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Administrator',
            'last_name' => 'Administrator',
            'email' => 'administrator@example.com',
            'dob' => '1990-01-01',
            'role' => 'Administrator',
        ]);

        User::create([
            'first_name' => 'Applicant',
            'last_name' => 'Applicant',
            'email' => 'applicant@example.com',
            'dob' => '1990-01-01',
            'role' => 'Applicant',
        ]);
    }
}
