<?php

namespace Tests\Controller;

use App\Models\Race;
use Tests\TestCase;

class RaceTest extends TestCase
{
    public function testGetAll()
    {

        Race::create([
            'name' => 'Test race',
            'distance' => '10k',
        ]);

        $this->get('/api/races')->assertJson([
            'data' => [
                [
                    'name' => 'Test race',
                    'distance' => '10k',
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

        $this->get('/api/races/' . $race->id)->assertJson([
            'data' => [
                'name' => 'Test race2',
                'distance' => '5k',
            ]
        ])->assertStatus(200);
    }

    public function testCreate()
    {
       $this->post('/api/races', [
            'name' => 'Test race3',
            'distance' => 'marathon',
        ])->assertJson([
            'data' => [
                'name' => 'Test race3',
                'distance' => 'marathon',
            ]
        ])->assertStatus(201);
    }

    public function testUpdate()
    {
        $race = Race::create([
            'name' => 'Test race4',
            'distance' => '5k',
        ]);

       $this->patch('/api/races/' . $race->id, [
            'distance' => '10k',
        ])->assertJson([
            'data' => [
                'name' => 'Test race4',
                'distance' => '10k',
            ]
        ])->assertStatus(200);
    }

    public function testDelete()
    {
        $race = Race::create([
            'name' => 'Test race4',
            'distance' => '5k',
        ]);
        $this->assertEquals(1, Race::count());
        $this->delete('/api/races/' . $race->id)->assertStatus(200);
        $this->assertEquals(0, Race::count());
    }
}
