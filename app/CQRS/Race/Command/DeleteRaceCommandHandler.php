<?php

namespace App\CQRS\Race\Command;

use App\Models\Race;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DeleteRaceCommandHandler
{
    #[CommandHandler]
    public function handle(DeleteRaceCommand $command): void
    {
        if (!Gate::allows('manage-race')) {
            throw new AccessDeniedHttpException();
        }

        $race = Race::findOrFail($command->getId());

        $race->delete();
    }
}
