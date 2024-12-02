<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use App\ESModule\Syncer\ModelMapper;

class ItemsRootModelFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
        private readonly DataFetcher $dataFetcher,
        private readonly EloquentAdapter $eloquentAdapter,
    ) {
    }

    public function fetch(array $items, SyncRowDto $syncRowDto): array
    {
        $className = $this->modelMapper->convertTableToClassName($syncRowDto->getTable());

        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix

                $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);
                dd('key:', $model->getKeyName());

                $items[$indexName] = $this->dataFetcher->fetch($index, $model, $syncRowDto);
            }
        }

        return $items;
    }
}
