<?php

namespace App\ESModule\Syncer\Creator\Document\Helper;

class ModelsRelatedValidatorAndGrouper
{
    /**
     * @return array<object>
     */
    public function validateAndGroup(array $modelsRelated): array
    {
        $modelsRelatedGrouped = [];

        foreach ($modelsRelated as $model) {
            $tableName = '';
            $identifier = '';

            $modelsRelatedGrouped[$tableName][$identifier] = $model;
        }

        return $modelsRelated;
    }
}
