<?php

namespace Tests\Controller;

use App\Models\Race;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->post('/api/auth/login', [
            'email' => 'administrator@example.com'
        ]);

        $token = $response->json()['token'];
        $this->assertNotNull($token);

        //test Unauthorized
        $this->get('/api/applications')->assertStatus(401);

        //test Authorized
        $this->withToken($token)->get('/api/applications')->assertStatus(200);
    }
}
