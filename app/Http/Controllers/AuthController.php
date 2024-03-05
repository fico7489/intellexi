<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthController extends Controller
{
    public function login() : JsonResponse
    {
        $credentials = request(['email', 'password']);

        //TODO exception
        $user = User::where(['email' => Request::get('email')])->first();
        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token,
        ], 200);
    }
}
