<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

use App\ESModule\Syncer\Creator\Document\Exception\DocumentCreatorException;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;

class ModelsRelatedValidatorAndGrouper
{
    public function __construct(
        private readonly ModelMapper $modelMapper,
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
