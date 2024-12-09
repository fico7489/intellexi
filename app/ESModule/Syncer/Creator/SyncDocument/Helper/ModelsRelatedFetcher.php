<?php

namespace App\ESModule\Syncer\Creator\SyncDocument\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncableItemDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class ModelsRelatedFetcher
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
        private readonly ModelsRelatedValidatorAndGrouper $modelsRelatedValidatorAndGrouper,
    ) {
    }

    /**
     * @return array<object>
     */
    public function fetch(SyncableItemDto $syncItemDto, IndexDto $indexDto, ?object $modelSource): array
    {
        $tableName = $syncItemDto->getTableName();

        $syncModels = [];
        // TODO decorate before syncModels
        $syncModels = $indexDto->getDefiner()->syncModels($syncModels);
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

        $classNameOrm = $indexDto->getClassNameOrm();
        $modelsRelated = $this->modelsRelatedValidatorAndGrouper->validateAndGroup($modelsRelated, $classNameOrm);

        return $modelsRelated;
    }
}
