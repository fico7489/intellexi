<?php

namespace App\ESModule\Syncer\Creator\Document\ModelMap\ModelsRelated;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Exception\Exception;

class ModelsRelatedValidatorAndGrouper
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @param array<object> $modelsRelated
     *
     * @return array<object>
     *
     * @throws Exception
     */
    public function validateAndGroup(array $modelsRelated, string $classNameOrmSource): array
    {
        $modelsRelatedGrouped = [];

        foreach ($modelsRelated as $model) {
            $tableName = $this->ormAdapter->fetchTableNameFromModel($model);
            $identifierValue = $this->ormAdapter->fetchIdentifierValueFromModel($model);

            $modelsRelatedGrouped[$tableName][$identifierValue] = $model;

            if (!$model instanceof $classNameOrmSource) {
                throw new Exception('Related model is not instanceof source classNameOrm="'.$classNameOrmSource.'"');
            }
        }

        $modelsRelatedNew = [];
        foreach ($modelsRelatedGrouped as $tableName => $models) {
            foreach ($models as $identifierValue => $model) {
                $modelsRelatedNew[] = $model;
            }
        }

        return $modelsRelatedNew;
    }
}
