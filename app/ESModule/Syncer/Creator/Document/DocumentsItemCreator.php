<?php

namespace App\ESModule\Syncer\Creator\Document;

use App\ESModule\Config\Related\Type\ModelClosureType;
use App\ESModule\Config\Related\Type\RelationType;
use App\ESModule\Config\Related\Type\RootType;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use App\ESModule\Syncer\Eloquent\EloquentAdapter;
use App\ESModule\Syncer\Eloquent\ModelMapper;
use App\ESModule\Syncer\Fetcher\DataFetcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
                    $className = $this->modelMapper->convertTableToClassName($tableName);
                    $index = $item2['index'];
                    $detection = $item2['detection'];
                    $updatingFields = $item2['updatingFields'];

                    if ($tableName === $syncRowDto->getTable()) {
                        $model = $this->eloquentAdapter->fetchModel($className, $syncRowDto);

                        if($detection instanceof RootType){
                            $models = [$model];
                        }elseif ($detection instanceof RelationType) {
                            $models = $model->{$detection->getRelation()};
                            $models = $models instanceof Collection ? $models : [$models];
                        }elseif ($detection instanceof ModelClosureType) {
                            $models = $detection->getClosure()($model);
                        }else{
                            continue;
                        }

                        //TODO make updates unique by model->id

                        foreach ($models as $model) {
                            if(!$model instanceof $className){
                                //throw new \Exception('TODO wron className');
                            }

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
