<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;

class DataFetcher
{
    public function __construct(
        private readonly EloquentAdapter $eloquentAdapter,
    ) {
    }

    public function fetch(
        IndexDefinerModelInterface $index,
        string $className,
        ChangedRowGroupedDto $changedRowGrouped,
    ) {
        $model = $this->eloquentAdapter->fetchModel($className, $changedRowGrouped);

        $data = [];

        // TODO decorate

        $data = $index->getData($data, $model);

        return $data;
    }
}
