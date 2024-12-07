<?php

namespace App\ESModule\Syncer\Creator\Document\ModelsRelated;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmMapper;
use App\ESModule\Syncer\Creator\Document\Exception\DocumentCreatorException;

class ModelsRelatedValidatorAndGrouper
{
    public function __construct(
        private readonly OrmMapper $modelMapper,
    ) {
    }

    /**
     * @param array<object> $modelsRelated
     *
     * @return array<object>
     *
     * @throws DocumentCreatorException
     */
    public function validateAndGroup(array $modelsRelated, string $classNameSource): array
    {
        $modelsRelatedGrouped = [];

        foreach ($modelsRelated as $model) {
            $tableName = $this->modelMapper->fetchTableNameFromModel($model);
            $identifierValue = $this->modelMapper->fetchIdentifierValueFromModel($model);

            $modelsRelatedGrouped[$tableName][$identifierValue] = $model;

            if (!$model instanceof $classNameSource) {
                throw new DocumentCreatorException('Related model is not instanceof source className="'.$classNameSource.'"');
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
