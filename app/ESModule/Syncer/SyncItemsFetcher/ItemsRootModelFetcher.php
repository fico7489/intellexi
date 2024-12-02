<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\ModelMapper;

class ItemsRootModelFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
    ) {
    }

    public function fetch(array $items, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $className = $this->modelMapper->convertTableToClassName($changedRowGrouped->getTable());

        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix
                $model = $this->eloquentAdapter->fetchModel($className, $changedRowGrouped);

                $items[$indexName] = $index->getData([], $model);
            }
        }

        return $items;
    }
}
