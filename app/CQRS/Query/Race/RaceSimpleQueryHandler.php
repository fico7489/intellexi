<?php

namespace App\CQRS\Query\Race;

use App\Models\Race;
use Ecotone\Modelling\Attribute\QueryHandler;

class RaceSimpleQueryHandler
{
    #[QueryHandler]
    public function handle(RaceSimpleQuery $query) : array
    {
        $race = Race::findOrFail($query->getId());

        return [
            'name' => $race->name,
            'distance' => $race->distance,
        ];
    }
}
