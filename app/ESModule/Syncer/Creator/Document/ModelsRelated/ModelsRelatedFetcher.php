<?php

namespace App\ESModule\Syncer\Creator\Document\ModelsRelated;

use App\ESModule\Config\Interface\IndexSyncInterface;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class ModelsRelatedFetcher
{
    private array $closuresExecuted = [];

    public function __construct(
        private readonly ModelMapper $modelMapper,
        private readonly ModelsRelatedValidatorAndGrouper $modelsRelatedValidatorAndGrouper,
    ) {
    }

    /**
     * @return array<object>
     */
    public function fetch(SyncItemDto $syncItemDto, IndexSyncInterface $index, ?object $modelSource): array
    {
        $tableName = $syncItemDto->getTableName();

        $syncModels = [];
        // TODO decorate before syncModels
        $syncModels = $index->syncModels($syncModels);
        // TODO decorate before syncModels

        // TODO decorators before $modelsRelated
        $modelsRelated = [];
        if ($this->modelMapper->isTableNameModel($tableName)) {
            $className = $this->modelMapper->convertTableNameToClassName($tableName);

            if (isset($syncModels[$className])) {
                $modelsRelated = $syncModels[$className]($modelSource, $syncItemDto, $modelsRelated);
            }
        }

        if (isset($syncModels[$tableName])) {
            $modelsRelated = $syncModels[$tableName]($modelSource, $syncItemDto, $modelsRelated);
        }

        // TODO decorators after $modelsRelated

        $className = $index->getClassName();
        $modelsRelated = $this->modelsRelatedValidatorAndGrouper->validateAndGroup($modelsRelated, $className);

        return $modelsRelated;
    }
}
