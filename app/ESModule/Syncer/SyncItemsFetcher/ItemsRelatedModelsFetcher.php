<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\ModelMapper;
use Illuminate\Database\Eloquent\Collection;

class ItemsRelatedModelsFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
    ) {
    }

    public function fetch(array $items, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $relatedSyncs = [];
        foreach ($this->configFetcher->fetchIndexes() as $index) {
            $syncRelations = $index->getSyncRelations();

            foreach ($syncRelations as $classNameRelated => $data) {
                foreach ($data as $relationRelated => $changedFields) {
                    $tableRelated = $this->modelMapper->convertClassNameToTable($classNameRelated);

                    $relatedSyncs[] = [
                        'table' => $tableRelated,
                        'className' => $classNameRelated,
                        'changedFields' => $changedFields,
                        'relation' => $relationRelated,
                        'index' => $index,
                    ];
                }
            }
        }

        foreach ($relatedSyncs as $table => $data) {
            $table = $data['table'];
            $className = $data['className'];
            $relation = $data['relation'];
            $index = $data['index'];

            if ($table === $changedRowGrouped->getTable()) {
                $model = $this->eloquentAdapter->fetchModel($className, $changedRowGrouped);

                $models = $model->{$relation};

                $models = $models instanceof Collection ? $models : [$models];

                foreach ($models as $model) {
                    $indexName = 'prefix_'.$index->getIndexName();
                    $items[$indexName] = $index->getData([], $model);
                }
            }
        }

        return $items;
    }
}
