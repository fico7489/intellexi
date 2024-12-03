<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Database\Eloquent\Collection;

class DocumentsItemCreator
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly EloquentAdapter $eloquentAdapter,
        private readonly DataFetcher $dataFetcher,
    ) {
    }

    /**
     * @return array<DocumentDto>
     */
    public function fetch(SyncRowDto $syncRowDto): array
    {
        $documents = [];

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
                        } else {
                            $models = $model;
                        }

                        $models = $models instanceof Collection ? $models : [$models];

                        foreach ($models as $model) {
                            $indexName = 'prefix_'.$index->getIndexName();
                            // TODO

                            // TODO
                            $identifier = $model->id;

                            $document = new DocumentDto(
                                $indexName,
                                $identifier,
                                $this->dataFetcher->fetch($index, $model),
                                SyncRowDto::TYPE_DELETE === $syncRowDto->getType() ? DocumentDto::TYPE_DELETE : DocumentDto::TYPE_UPSERT,
                            );

                            $documents[] = $document;
                        }
                    }
                }
            }
        }

        return $documents;
    }
}
