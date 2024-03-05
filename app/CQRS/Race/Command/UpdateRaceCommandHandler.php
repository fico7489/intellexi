<?php

namespace App\CQRS\Race\Command;

use App\Models\Race;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UpdateRaceCommandHandler
{
    #[CommandHandler]
    public function handle(UpdateRaceCommand $command): void
    {
        if (!Gate::allows('manage-race')) {
            throw new AccessDeniedHttpException();
        }

        $race = Race::findOrFail($command->getId());

        $race->name = $command->getName() ?? $race->name;
        $race->distance = $command->getDistance() ?? $race->distance;

        $race->save();
    }
}
