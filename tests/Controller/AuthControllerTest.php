<?php

namespace Tests\Controller;

use App\Models\Race;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
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

        $user = User::where([])->first();

        $this->withToken($token)->get('/api/applications', [

        ])->assertStatus(200);
    }
}
