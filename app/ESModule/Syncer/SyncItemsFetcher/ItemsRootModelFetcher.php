<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;

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

                // TODO
                $identifier = $model->id;

                $items[$indexName][$identifier] = $this->dataFetcher->fetch($index, $model);
            }
        }

        return $items;
    }
}
