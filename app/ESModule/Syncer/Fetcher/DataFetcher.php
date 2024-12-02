<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use Illuminate\Database\Eloquent\Model;

class DataFetcher
{
    public function fetch(
        IndexDefinerModelInterface $index,
        Model $model,
        ChangedRowGroupedDto $changedRowGrouped,
    ) {
        $data = [];

        // TODO decorate

        $data = $index->getData($data, $model);

        return $data;
    }
}
