<?php

namespace App\ESModule\Syncer;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Config\ConfigFetcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SyncItemsFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
    ) {
    }

    public function fetch(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $items = [];

        $items = $this->addRootModelForSync($items, $changedRowGrouped);

        $items = $this->addRelatedModelsForSync($items, $changedRowGrouped);

        return $items;
    }

    private function addRootModelForSync(array $items, ChangedRowGroupedDto $changedRowGrouped): array
    {
        $className = $this->modelMapper->convertTableToClassName($changedRowGrouped->getTable());

        foreach ($this->configFetcher->fetchIndexes() as $index) {
            if ($className === $index->getClassName()) {
                $indexName = 'prefix_'.$index->getIndexName(); // TODO prefix
                $model = $this->fetchModel($className, $changedRowGrouped);

                $items[$indexName] = $index->getData([], $model);
            }
        }

        return $items;
    }

    private function addRelatedModelsForSync(array $items, ChangedRowGroupedDto $changedRowGrouped): array
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
                $model = $this->fetchModel($className, $changedRowGrouped);

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

    private function fetchModel(string $className, ChangedRowGroupedDto $changedRowGrouped): ?Model
    {
        return $className::find($changedRowGrouped->getIdentifier());
    }
}
