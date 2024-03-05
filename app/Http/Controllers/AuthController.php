<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(): JsonResponse
    {
        $user = User::where(['email' => Request::get('email')])->first();

        if (!$user) {
            throw new UnauthorizedHttpException();
        }

        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token,
        ], 200);
    }
}
