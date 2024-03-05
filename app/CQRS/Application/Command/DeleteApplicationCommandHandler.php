<?php

namespace App\CQRS\Application\Command;

use App\Models\Application;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DeleteApplicationCommandHandler
{
    #[CommandHandler]
    public function handle(DeleteApplicationCommand $command): void
    {
        $application = Application::findOrFail($command->getId());

        if (!Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        $application->delete();
    }
}
