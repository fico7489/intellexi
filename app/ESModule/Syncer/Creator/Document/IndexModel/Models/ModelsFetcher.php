<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel\Models;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class ModelsFetcher
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
        private readonly ModelsValidatorAndGrouper $modelsRelatedValidatorAndGrouper,
    ) {
    }

    /**
     * @return array<object>
     */
    public function fetch(CdcSyncableDto $syncItemDto, IndexDto $indexDto, ?object $modelSource): array
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
