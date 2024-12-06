<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\Interface\IndexSyncInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class ModelsRelatedFetcher
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
    ) {
    }

    /**
     * @return array<object>
     */
    public function fetch(SyncItemDto $syncItemDto, IndexSyncInterface $index, ?object $modelRoot): array
    {
        $tableName = $syncItemDto->getTableName();
        $syncModels = $index->syncModels([]);

        //TODO decorators before
        $modelsRelated = [];
        if ($this->modelMapper->isTableNameModel($tableName)) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);

            if (isset($syncModels[$className])) {
                $modelsRelated = $syncModels[$className]($modelRoot, $syncItemDto, $modelsRelated);
            }
        }

        if (isset($syncModels[$tableName])) {
            $modelsRelated = $syncModels[$tableName]($modelRoot, $syncItemDto, $modelsRelated);
        }

        //TODO decorators after

        return $modelsRelated;
    }
}
