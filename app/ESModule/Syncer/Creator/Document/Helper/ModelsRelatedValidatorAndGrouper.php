<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class ModelsRelatedValidatorAndGrouper
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
    )
    {
    }

    /**
     * @return array<object>
     */
    public function validateAndGroup(array $modelsRelated): array
    {
        $modelsRelatedGrouped = [];

        foreach ($modelsRelated as $model) {
            $tableName = $this->modelMapper->fetchTableNameFromModel($model);
            $identifierValue = $this->modelMapper->fetchIdentifierValueFromModel($model);

            $modelsRelatedGrouped[$tableName][$identifierValue] = $model;
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
