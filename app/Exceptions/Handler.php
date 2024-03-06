<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->reportable(function (\Throwable $e) {
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

        $this->renderable(function (ValidationException $e, Request $request) {
            return $this->reportGenericApiException(422, 'Validation Exception', ['errors' => $e->errors()]);
        });

        $this->renderable(function (\Throwable $e, Request $request) {
            return $this->reportGenericApiException(500, 'Internal error');
        });
    }

    private function reportGenericApiException(int $code, string $message, array $custom = []): \Illuminate\Http\JsonResponse
    {
        return response()->json(array_merge([
            'code' => $code,
            'message' => $message,
        ], $custom), $code);
    }
}
