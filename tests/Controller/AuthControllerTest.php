<?php

namespace Tests\Controller;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'administrator@example.com',
        ]);

        $token = $response->json()['token'];
        $this->assertNotNull($token);

        // test Unauthorized
        $this->get('/api/applications')->assertStatus(401);

        // test Authorized
        $this->withToken($token)->get('/api/applications')->assertStatus(200);
    }
}
