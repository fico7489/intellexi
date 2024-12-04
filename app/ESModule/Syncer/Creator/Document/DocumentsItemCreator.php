<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\RelatedSync\FetchType\ClosureFetchType;
use App\ESModule\Config\RelatedSync\FetchType\RelationFetchType;
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
    public function create(SyncRowDto $syncRowDto): array
    {
        $documents = [];

        $updatingMap = $this->modelMapper->fetchDatabaseMapping();

        foreach ($updatingMap as $databaseName => $data) {
            foreach ($data as $tableName => $items2) {
                foreach ($items2 as $item2) {
                    $index = $item2['index'];
                    $fetchType = $item2['fetchType'];
                    $updatingFields = $item2['updatingFields'];

                    if ($tableName === $syncRowDto->getTableName()) {
                        $classNames = $this->modelMapper->fetchAllClassNames();

                        if (isset($classNames[$tableName])){
                            $className = $this->modelMapper->convertTableNameToClassName($tableName);
                            $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);

                            if (null === $fetchType) {
                                $models = [$model];
                            } elseif ($fetchType instanceof RelationFetchType) {
                                $models = $model->{$fetchType->getRelation()};
                                $models = $models instanceof Collection ? $models : [$models];
                            } elseif ($fetchType instanceof ClosureFetchType) {
                                $models = $fetchType->getClosure()($model, $syncRowDto);
                            } else {
                                continue;
                            }
                        }else{
                            $models = $fetchType->getClosure()($tableName, $syncRowDto);
                        }

                        // TODO make updates unique by model->id

                        foreach ($models as $model) {
                            //if (!$model instanceof $className) {
                                // throw new \Exception('TODO wrong className');
                            //}

                            // TODO prefix
                            $indexName = 'prefix_'.$index->getIndexName();

                            // TODO
                            $identifierValue = $model->id;

                            $document = new DocumentDto(
                                $indexName,
                                $identifierValue,
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
