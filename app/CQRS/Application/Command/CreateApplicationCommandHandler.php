<?php

namespace App\CQRS\Application\Command;

use App\Models\Application;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateApplicationCommandHandler
{
    #[CommandHandler]
    public function handle(CreateApplicationCommand $command): void
    {
        $data = [
            'first_name' => $command->getFirstName(),
            'last_name' => $command->getLastName(),
            'club' => $command->getClub(),
            'race_id' => $command->getRaceId(),
        ];

        Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'club' => '',
            'race_id' => 'required',
        ])->validate();

        Application::create(array_merge($data, ['user_id' => Auth::user()->id]));
    }
}
