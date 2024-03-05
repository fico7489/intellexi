<?php

namespace App\CQRS\Race\Query\Race;

use App\Models\Race;
use Ecotone\Modelling\Attribute\QueryHandler;

class RacesSimpleQueryHandler
{
    #[QueryHandler]
    public function handle(RacesSimpleQuery $query) : array
    {
        $races = Race::all();

        $data = [];
        foreach ($races as $race) {
            $data[] = [
                'name' => $race->name,
                'distance' => $race->distance,
            ];
        }


        return $data;
    }
}
