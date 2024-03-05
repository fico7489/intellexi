<?php

namespace App\Http\Controllers;

use App\CQRS\Application\Command\CreateApplicationCommand;
use App\CQRS\Application\Command\DeleteApplicationCommand;
use App\CQRS\Application\Query\ApplicationSimpleQuery;
use App\CQRS\Application\Query\ApplicationsSimpleQuery;
use Ecotone\Modelling\CommandBus;
use Ecotone\Modelling\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class ApplicationController extends Controller
{
    public function __construct(private readonly CommandBus $commandBus, private readonly QueryBus $queryBus)
    {
    }

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
        $this->commandBus->send(new CreateApplicationCommand(
            Request::get('first_name'),
            Request::get('last_name'),
            Request::get('club'),
            Request::get('race_id'),
        ));

        return $this->emptyResponse(201);
    }

    public function delete($id): JsonResponse
    {
        $this->commandBus->send(new DeleteApplicationCommand($id));

        return $this->emptyResponse();
    }
}
