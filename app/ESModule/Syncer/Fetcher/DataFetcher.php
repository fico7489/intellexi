<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class DataFetcher
{
    public function fetch(IndexDto $indexDto, object $model): array
    {
        $data = [];

        // TODO decorate

        $data = $indexDto->getDefiner()->getData($data, $model);

        return $data;
    }
}
