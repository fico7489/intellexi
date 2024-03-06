<?php

namespace App\Http\Controllers;

use App\CQRS\Race\Command\CreateRaceCommand;
use App\CQRS\Race\Command\DeleteRaceCommand;
use App\CQRS\Race\Command\UpdateRaceCommand;
use App\CQRS\Race\Query\RaceSimpleQuery;
use App\CQRS\Race\Query\RacesSimpleQuery;
use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class RaceController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus, private readonly QueryBus $queryBus)
    {
    }

    public function getAll(): JsonResponse
    {
        return $this->dataResponse($this->queryBus->send(new RacesSimpleQuery()));
    }

    public function get($id): JsonResponse
    {
        return $this->dataResponse($this->queryBus->send(new RaceSimpleQuery($id)));
    }

    public function create(): JsonResponse
    {
        $this->commandBus->send(new CreateRaceCommand(Request::get('name'), Request::get('distance')));

        return $this->emptyResponse(201);
    }

    public function update($id): JsonResponse
    {
        $this->commandBus->send(new UpdateRaceCommand($id, Request::get('name'), Request::get('distance')));

        return $this->emptyResponse(200);
    }

    public function delete($id): JsonResponse
    {
        $this->commandBus->send(new DeleteRaceCommand($id));

        return $this->emptyResponse();
    }
}
