<?php

namespace Tests\ErrorHandler;

use App\Models\Race;
use Tests\TestCase;

class ErrorHandlerTest extends TestCase
{
    public function testErrorHandler()
    {
        $response = $this->json('GET', '/api/races/'. 1)->assertStatus(401)->json();
        $this->assertEquals('Unauthorized', $response['message']);
        $this->assertEquals(401, $response['code']);

        $race = Race::create(['name' => 'Test race', 'distance' => '5k']);
        $response = $this->asApplicant()->json('PATCH', '/api/races/'.$race->id, [])->assertStatus(403)->json();
        $this->assertEquals('Access Denied', $response['message']);
        $this->assertEquals(403, $response['code']);

        $response = $this->asAdministrator()->json('GET', '/api/races/1')->assertStatus(404)->json();
        $this->assertEquals('Not found', $response['message']);
        $this->assertEquals(404, $response['code']);

        $response = $this->asAdministrator()->json('POST', '/api/races', ['distance' => 'test'])->assertStatus(422)->json();
        $this->assertEquals('Validation Exception', $response['message']);
        $this->assertEquals(422, $response['code']);
        $this->assertEquals([
            'name' => [
                'The name field is required.',
            ],
            'distance' => [
                'The selected distance is invalid.',
            ],
        ], $response['errors']);

        $response = $this->asAdministrator()->json('GET', '/api/test/exception/500')->assertStatus(500)->json();
        $this->assertEquals('Internal error', $response['message']);
        $this->assertEquals(500, $response['code']);
    }
}
