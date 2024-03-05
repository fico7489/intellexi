<?php

namespace App\CQRS\Application\Query;

use App\Models\Application;
use Ecotone\Modelling\Attribute\QueryHandler;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApplicationSimpleQueryHandler
{
    #[QueryHandler]
    public function handle(ApplicationSimpleQuery $query): array
    {
        $application = Application::findOrFail($query->getId());

        if (!Gate::allows('manage-application', $application)) {
            throw new AccessDeniedHttpException();
        }

        return [
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'club' => $application->club,
            'race_id' => $application->race_id,
            'user_id' => $application->user_id,
        ];
    }
}
