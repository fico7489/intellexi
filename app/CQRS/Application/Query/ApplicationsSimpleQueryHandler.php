<?php

namespace App\CQRS\Application\Query;

use App\Models\Application;
use Ecotone\Modelling\Attribute\QueryHandler;
use Illuminate\Support\Facades\Auth;

class ApplicationsSimpleQueryHandler
{
    #[QueryHandler]
    public function handle(ApplicationsSimpleQuery $query): array
    {
        $applications = Application::where([]);

        if ('Applicant' === Auth::user()->role) {
            $applications->where(['user_id' => Auth::user()->id]);
        }

        $applications = $applications->get();

        $data = [];
        foreach ($applications as $application) {
            $data[] = [
                'first_name' => $application->first_name,
                'last_name' => $application->last_name,
                'club' => $application->club,
                'race_id' => $application->race_id,
                'user_id' => $application->user_id,
            ];
        }

        return $data;
    }
}
