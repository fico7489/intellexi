<?php

namespace App\ESModule\Syncer\Creator\Document\IndexModel\Models;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Exception\Exception;

class ModelsValidatorAndGrouper
{
    public function __construct(
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    /**
     * @param array<object> $models
     *
     * @return array<object>
     *
     * @throws Exception
     */
    public function validateAndGroup(array $models, string $classNameOrmSource): array
    {
        $modelsGrouped = [];

        foreach ($models as $model) {
            $tableName = $this->ormAdapter->fetchTableNameFromModel($model);
            $identifierValue = $this->ormAdapter->fetchIdentifierValueFromModel($model);

            $modelsGrouped[$tableName][$identifierValue] = $model;

            if (!$model instanceof $classNameOrmSource) {
                throw new Exception('Related model is not instanceof source classNameOrm="'.$classNameOrmSource.'"');
            }
        }

        $modelsFlattened = [];
        foreach ($modelsGrouped as $tableName => $tableModels) {
            foreach ($tableModels as $identifierValue => $model) {
                $modelsFlattened[] = $model;
            }
        }

        return $modelsFlattened;
    }
}
