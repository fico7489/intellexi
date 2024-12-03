<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Database\Eloquent\Collection;

class ItemsRelatedModelsFetcher
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    public function fetch(array $items, SyncRowDto $syncRowDto): array
    {
        $updatingMap = $this->modelMapper->fetchDatabaseMapping();

        foreach ($updatingMap as $databaseName => $data) {
            foreach ($data as $tableName => $items2) {
                foreach ($items2 as $item2) {
                    $className = $this->modelMapper->convertTableToClassName($tableName);
                    $index = $item2['index'];
                    $relation = $item2['relation'];
                    $updatingFields = $item2['updatingFields'];

                    if ($tableName === $syncRowDto->getTable()) {
                        $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);

                        if ($relation) {
                            // TODO by type, closure, relation or root
                            $models = $model->{$relation};
                        }else{
                            $models = $model;
                        }

                        $models = $models instanceof Collection ? $models : [$models];

                        foreach ($models as $model) {
                            $indexName = 'prefix_'.$index->getIndexName();
                            // TODO

                            // TODO
                            $identifier = $model->id;

                            $items[$indexName][$identifier] = $this->dataFetcher->fetch($index, $model);
                        }
                    }
                }
            }
        }

        return $items;
    }
}
