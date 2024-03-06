<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class Authenticate
{
    public function handle(Request $request, \Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            /** @var \Tymon\JWTAuth\Payload $payload */
            $payload = JWTAuth::decode(new Token($token));
            $id = $payload->toArray()['sub'];

            $user = User::where(['id' => $id])->first();

            Auth::login($user);
        } catch (\Throwable $t) {
            // TODO more fine grained exceptions
            throw new UnauthorizedHttpException('Unauthorized');
        }

        return $next($request);
    }
}
