<?php

namespace App\ESModule\Syncer\Creator\Document\ModelsRelated;

use App\ESModule\Config\Interface\IndexSyncInterface;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;

class ModelsRelatedFetcher
{
    private array $closuresExecuted = [];

    public function __construct(
        private readonly OrmAdapter $ormAdapter,
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
        if ($this->ormAdapter->isTableNameOrm($tableName)) {
            $classNameOrm = $this->ormAdapter->convertTableNameToClassNameOrm($tableName);

            if (isset($syncModels[$classNameOrm])) {
                $modelsRelated = $syncModels[$classNameOrm]($modelSource, $syncItemDto, $modelsRelated);
            }
        }

        if (isset($syncModels[$tableName])) {
            $modelsRelated = $syncModels[$tableName]($modelSource, $syncItemDto, $modelsRelated);
        }

        // TODO decorators after $modelsRelated

        $classNameOrm = $index->getClassName();
        $modelsRelated = $this->modelsRelatedValidatorAndGrouper->validateAndGroup($modelsRelated, $classNameOrm);

        return $modelsRelated;
    }
}
