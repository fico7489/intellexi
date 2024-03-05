<?php

namespace Tests\Controller;

use App\Models\Application;
use App\Models\Race;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testShowAll()
    {
        $race = Race::create([
            'name' => 'Test race2',
            'distance' => '5k',
        ]);

        $application = Application::create([
            'first_name' => 'First_name',
            'last_name' => 'Last_name',
            'club' => 'Club',
            'race_id' => $race->id,
        ]);

        $this->json('GET', '/api/applications')->assertJson([
            'data' => [
                [
                    'first_name' => 'First_name',
                    'last_name' => 'Last_name',
                    'club' => 'Club',
                    'race_id' => $race->id,
                ]
            ]
        ])->assertStatus(200);
    }

    public function testGet()
    {
        $race = Race::create([
            'name' => 'Test race2',
            'distance' => '5k',
        ]);

        $application = Application::create([
            'first_name' => 'First_name',
            'last_name' => 'Last_name',
            'club' => 'Club',
            'race_id' => $race->id,
        ]);

        $this->json('GET', '/api/applications/' . $application->id)->assertJson([
            'data' => [
                'first_name' => 'First_name',
                'last_name' => 'Last_name',
                'club' => 'Club',
                'race_id' => $race->id,
            ]
        ])->assertStatus(200);
    }

    public function testUpdate()
    {
        $race = Race::create([
            'name' => 'Test race2',
            'distance' => '5k',
        ]);

        $this->json('POST', '/api/applications', [
            'first_name' => 'First_name',
            'last_name' => 'Last_name',
            'club' => 'Club',
            'race_id' => $race->id,
        ])->assertJson([
            'data' => [
                'first_name' => 'First_name',
                'last_name' => 'Last_name',
                'club' => 'Club',
                'race_id' => $race->id,
            ]
        ])->assertStatus(201);
    }

    public function testDelete()
    {
        $race = Race::create([
            'name' => 'Test race2',
            'distance' => '5k',
        ]);

        $application = Application::create([
            'first_name' => 'First_name',
            'last_name' => 'Last_name',
            'club' => 'Club',
            'race_id' => $race->id,
        ]);

        $this->assertEquals(1, Application::count());
        $this->json('DELETE', '/api/applications/' . $application->id)->assertStatus(200);
        $this->assertEquals(0, Application::count());
    }
}
