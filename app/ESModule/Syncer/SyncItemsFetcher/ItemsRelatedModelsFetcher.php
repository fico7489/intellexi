<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\Related\SyncRelationDto;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Database\Eloquent\Collection;

class ItemsRelatedModelsFetcher
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    public function fetch(array $items, SyncRowDto $syncRowDto): array
    {
        $updatingMap = $this->constructMap();

        dump('updating map:', $updatingMap);

        foreach ($updatingMap as $table => $data) {
            $table = $data['table'];
            $className = $data['className'];
            $relation = $data['relation'];
            $index = $data['index'];

            if ($table === $syncRowDto->getTable()) {
                $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);

                $models = $model->{$relation};

                $models = $models instanceof Collection ? $models : [$models];

                foreach ($models as $model) {
                    $indexName = 'prefix_'.$index->getIndexName();
                    // TODO

                    $items[$indexName] = $this->dataFetcher->fetch($index, $model, $syncRowDto);
                }
            }
        }

        return $items;
    }

    private function constructMap(): array
    {
        $relatedSyncs = [];
        foreach ($this->configFetcher->fetchIndexes() as $index) {
            $syncRelations = $index->getSyncRelations();

            foreach ($syncRelations as $syncRelationDto) {
                /** @var SyncRelationDto $syncRelationDto */
                $className = $syncRelationDto->getClassName();
                $relation = $syncRelationDto->getRelation();
                $includedFields = $syncRelationDto->getUpdatingFields();

                $tableRelated = $this->modelMapper->convertClassNameToTable($className);

                $relatedSyncs[] = [
                    'table' => $tableRelated,
                    'className' => $className,
                    'relation' => $relation,
                    'changedFields' => $includedFields,
                    'index' => $index,
                ];
            }
        }

        return $relatedSyncs;
    }
}
