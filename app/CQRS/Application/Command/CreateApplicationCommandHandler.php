<?php

namespace App\CQRS\Application\Command;

use App\Models\Application;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Auth;

class CreateApplicationCommandHandler
{
    #[CommandHandler]
    public function handle(CreateApplicationCommand $command): void
    {
        $application = Application::create([
            'first_name' => $command->getFirstName(),
            'last_name' => $command->getLastName(),
            'club' => $command->getClub(),
            'race_id' => $command->getRaceId(),
            'user_id' => Auth::user()->id,
        ]);
    }
}
