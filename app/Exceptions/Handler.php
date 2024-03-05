<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (UnauthorizedHttpException $e, Request $request) {
            return $this->reportGenericApiException(401, 'Unauthorized');
        });

        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            return $this->reportGenericApiException(403, 'Access Denied');
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return $this->reportGenericApiException(404, 'Not found');
        });

        $this->renderable(function (Throwable $e, Request $request) {
            return $this->reportGenericApiException(500, 'Internal error');
        });
    }

    private function reportGenericApiException(int $code, string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message
        ], $code);
    }
}
