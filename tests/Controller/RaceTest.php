<?php

namespace Tests\Controller;

use App\Models\Race;
use Tests\TestCase;

class RaceTest extends TestCase
{
    public function testGetAll()
    {
        $race = $this->createRaceModel();
        $race2 = $this->createRaceModel();

        $this->asApplicant()->get('/api/races')->assertJson([
            'data' => [
                [
                    'name' => 'Test race',
                    'distance' => '5k',
                ],
                [
                    'name' => 'Test race',
                    'distance' => '5k',
                ],
            ],
        ])->assertStatus(200);
    }

    public function testGet()
    {
        $race = $this->createRaceModel();

        $this->asApplicant()->get('/api/races/'.$race->id)->assertJson([
            'data' => [
                'name' => 'Test race',
                'distance' => '5k',
            ],
        ])->assertStatus(200);
    }

    public function testCreate()
    {
        $data = [
            'name' => 'Test race3',
            'distance' => 'Marathon',
        ];

        $this->asAdministrator()->post('/api/races', $data)->assertStatus(201);

        $this->asApplicant()->post('/api/races', $data)->assertStatus(403);
    }

    public function testUpdate()
    {
        $race = $this->createRaceModel();

        $this->asAdministrator()->patch('/api/races/'.$race->id, [
            'distance' => '10k',
        ])->assertStatus(200);

        $this->asApplicant()->patch('/api/races/'.$race->id, [])->assertStatus(403);
    }

    public function testDelete()
    {
        $race = $this->createRaceModel();

        $this->assertEquals(1, Race::count());
        $this->asAdministrator()->delete('/api/races/'.$race->id)->assertStatus(200);
        $this->assertEquals(0, Race::count());

        $this->asApplicant()->delete('/api/races/'.$race->id)->assertStatus(403);
    }

    private function createRaceModel()
    {
        return Race::create([
            'name' => 'Test race',
            'distance' => '5k',
        ]);
    }
}
