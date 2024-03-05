<?php

namespace App\Http\Controllers;

use App\CQRS\Application\Query\ApplicationSimpleQuery;
use App\CQRS\Application\Query\ApplicationsSimpleQuery;
use App\CQRS\Race\Query\Race\RaceSimpleQuery;
use App\CQRS\Race\Query\Race\RacesSimpleQuery;
use App\Models\Application;
use App\Models\Race;
use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApplicationController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus, private readonly QueryBus $queryBus) {}

    public function getAll(): JsonResponse
    {
        return $this->dataResponse($this->queryBus->send(new ApplicationsSimpleQuery()));
    }

    public function get($id): JsonResponse
    {
        return $this->dataResponse($this->queryBus->send(new ApplicationSimpleQuery($id)));
    }

    public function create(): JsonResponse
    {
        $application = Application::create(array_merge(Request::all(), ['user_id' => Auth::user()->id]));

        if (! Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse([
            'data' => [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
                'user_id' => $application->user_id,
            ]
        ], 201);
    }

    public function delete($id): JsonResponse
    {
        $application = Application::findOrFail($id);

        if (! Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        $application->delete();

        return new JsonResponse([]);
    }
}
