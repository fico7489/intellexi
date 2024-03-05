<?php

namespace App\CQRS\Race\Command;

use App\Models\Race;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateRaceCommandHandler
{
    #[CommandHandler]
    public function handle(CreateRaceCommand $command): void
    {
        if (!Gate::allows('manage-race')) {
            throw new AccessDeniedHttpException();
        }

        request()->validate([
            'name' => 'required|max:255',
            'distance' => 'in:'.implode(',', Race::RACES),
        ]);

        $race = new Race();
        $race->name = $command->getName();
        $race->distance = $command->getDistance();
        $race->save();
    }
}
