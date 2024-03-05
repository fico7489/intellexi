<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        /** @var \Tymon\JWTAuth\Payload $payload */
        $payload = JWTAuth::decode(new Token($token));
        $id = $payload->toArray()['sub'];

        $user = User::where(['id' => $id])->first();

        Auth::login($user);

        return $next($request);
    }
}
