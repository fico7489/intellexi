<?php

namespace Tests\Controller;

use App\Models\Application;
use App\Models\Race;
use App\Models\User;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testShowAll()
    {
        $userApplicant = $this->findUserByEmail('applicant@example.com');
        $userApplicant2 = $this->findUserByEmail('applicant2@example.com');

        $application = $this->createApplicationModel($userApplicant);
        $application2 = $this->createApplicationModel($userApplicant2);

        // Administrator can see all
        $this->asAdministrator()->json('GET', '/api/applications')->assertJson([
            'data' => [
                [
                    'first_name' => 'First_name',
                    'last_name' => 'Last_name',
                    'club' => 'Club',
                    'race_id' => $application->race->id,
                    'user_id' => $application->user_id,
                ],
                [
                    'first_name' => 'First_name',
                    'last_name' => 'Last_name',
                    'club' => 'Club',
                    'race_id' => $application2->race->id,
                    'user_id' => $application2->user_id,
                ],
            ],
        ])->assertJsonCount(2, 'data')->assertStatus(200);

        // Applicant can see own
        $this->asApplicant()->json('GET', '/api/applications')
            ->assertJson([
                'data' => [
                    [
                        'first_name' => 'First_name',
                        'last_name' => 'Last_name',
                        'club' => 'Club',
                        'race_id' => $application->race->id,
                        'user_id' => $application->user_id,
                    ],
                ],
            ])->assertJsonCount(1, 'data')->assertStatus(200);

        // Applicant2 can see own
        $this->asApplicant2()->json('GET', '/api/applications')
            ->assertJson([
                'data' => [
                    [
                        'first_name' => 'First_name',
                        'last_name' => 'Last_name',
                        'club' => 'Club',
                        'race_id' => $application2->race->id,
                        'user_id' => $application2->user_id,
                    ],
                ],
            ])->assertJsonCount(1, 'data')->assertStatus(200);
    }

    public function testGet()
    {
        $userApplicant = $this->findUserByEmail('applicant@example.com');
        $userApplicant2 = $this->findUserByEmail('applicant2@example.com');

        $application = $this->createApplicationModel($userApplicant);
        $application2 = $this->createApplicationModel($userApplicant2);

        // Administrator can see all
        $this->asAdministrator()->json('GET', '/api/applications/'.$application->id)->assertStatus(200);
        $this->asAdministrator()->json('GET', '/api/applications/'.$application2->id)->assertStatus(200);

        // Applicant can see own
        $this->asApplicant()->json('GET', '/api/applications/'.$application->id)->assertStatus(200);
        $this->asApplicant()->json('GET', '/api/applications/'.$application2->id)->assertStatus(403);

        // Applicant2 can see own
        $this->asApplicant2()->json('GET', '/api/applications/'.$application->id)->assertStatus(403);
        $this->asApplicant2()->json('GET', '/api/applications/'.$application2->id)->assertStatus(200);
    }

    public function testCreate()
    {
        $userApplicant = $this->findUserByEmail('applicant@example.com');
        $userApplicant2 = $this->findUserByEmail('applicant2@example.com');
        $application = $this->createApplicationModel($userApplicant);

        $this->asApplicant()->json('POST', '/api/applications', [
            'first_name' => 'First_name',
            'last_name' => 'Last_name',
            'club' => 'Club',
            'race_id' => $application->race->id,
        ])->assertJson([
            'data' => [
                'first_name' => 'First_name',
                'last_name' => 'Last_name',
                'club' => 'Club',
                'race_id' => $application->race->id,
            ],
        ])->assertStatus(201);
    }

    public function testDelete()
    {
        $userApplicant = $this->findUserByEmail('applicant@example.com');
        $userApplicant2 = $this->findUserByEmail('applicant2@example.com');
        $application = $this->createApplicationModel($userApplicant);

        $this->assertEquals(1, Application::count());
        $this->asApplicant2()->json('DELETE', '/api/applications/'.$application->id)->assertStatus(403);
        $this->asApplicant()->json('DELETE', '/api/applications/'.$application->id)->assertStatus(200);

        $this->assertEquals(0, Application::count());
    }

    private function createApplicationModel(User $user)
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
            'user_id' => $user->id,
        ]);

        return $application;
    }
}
