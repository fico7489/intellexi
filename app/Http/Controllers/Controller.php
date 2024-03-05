<?php

namespace App\Http\Controllers;

use App\CQRS\Query\Race\RacesSimpleQuery;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function dataResponse(array $data): JsonResponse
    {
        return new JsonResponse([
            'data' => $data,
        ], 200);
    }

    protected function emptyResponse(int $status = 200): JsonResponse
    {
        return new JsonResponse([], $status);
    }
}
