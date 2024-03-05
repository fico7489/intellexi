<?php

namespace App\CQRS\Race\Command;

use App\Models\Race;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateRaceCommandHandler
{
    #[CommandHandler]
    public function handle(CreateRaceCommand $command): void
    {
        if (!Gate::allows('manage-race')) {
            throw new AccessDeniedHttpException();
        }

        $data = [
            'name' => $command->getName(),
            'distance' => $command->getDistance(),
        ];

        Validator::make($data, [
            'name' => 'required|max:255',
            'distance' => 'in:'.implode(',', Race::RACES),
        ])->validate();

        Race::create($data);
    }
}
