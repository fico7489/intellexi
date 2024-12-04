<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\SyncType\ModelFetchType\ModelClosureFetchType;
use App\ESModule\Config\SyncType\ModelFetchType\ModelRelationFetchType;
use App\ESModule\Config\SyncType\RelatedModelSync;
use App\ESModule\Config\SyncType\RelatedTableSync;
use App\ESModule\Config\SyncType\RootSync;
use App\ESModule\Config\SyncType\TableFetchType\TableClosureFetchType;
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

        foreach ($updatingMap as $databaseName => $databaseData) {
            foreach ($databaseData as $tableName => $tableData) {
                foreach ($tableData as $sync) {
                    /** @var IndexDefinerModelInterface $index */
                    $index = $sync['index'];

                    /** @var RootSync|RelatedTableSync|RelatedModelSync $type */
                    $type = $sync['type'];

                    if ($tableName === $syncRowDto->getTableName()) {
                        $classNames = $this->modelMapper->fetchAllClassNames();

                        $model = null;
                        if (isset($classNames[$tableName])) {
                            $className = $this->modelMapper->convertTableNameToClassName($tableName);
                            $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);
                        }

                        if ($type instanceof RootSync) {
                            $models = [$model];
                        } elseif ($type instanceof RelatedModelSync) {
                            if ($type->getFetchType() instanceof ModelRelationFetchType) {
                                $models = $model->{$type->getFetchType()->getRelation()};
                                $models = $models instanceof Collection ? $models : [$models];
                            } elseif ($type->getFetchType() instanceof ModelClosureFetchType) {
                                $models = $type->getFetchType()->getClosure()($model, $syncRowDto);
                            }
                        } elseif ($type instanceof RelatedTableSync) {
                            if ($type->getFetchType() instanceof TableClosureFetchType) {
                                $models = $type->getFetchType()->getClosure()($syncRowDto);
                            }
                        } else {
                            continue;
                        }

                        // TODO make updates unique by model->id

                        foreach ($models as $model) {
                            // if (!$model instanceof $className) {
                            // throw new \Exception('TODO wrong className');
                            // }

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
