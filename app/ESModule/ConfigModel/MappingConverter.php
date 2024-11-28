<?php

namespace App\ESModule\ConfigModel;

use App\ESModule\Interface\IndexDefinerModelInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MappingConverter
{
    //convert configModel to mapping
    public function convertMapping(IndexDefinerModelInterface $index): array
    {
        $configModel = $index->getConfigModel();
        $className = $index->getClassName();

        return $this->fetchMapping($configModel, new $className);
    }

    private function fetchMapping(array $configModel, $model): array
    {
        $mapping = [];
        foreach ($configModel as $key => $value) {
            if (is_int($key)) {
                $propertyName = $value;

                $mapping[$propertyName] = ['type' => 'string'];
            } else {
                $relationName = $key;
                $configModelRelated = $value;

                /** @var BelongsTo $relation */
                $relation = (new $model())->{$relationName}();

                $type = $relation instanceof BelongsTo ? 'object' : 'nested';
                $modelRelated = $relation->getRelated();

                $mapping[$relationName] = [
                    'type' => $type,
                    'properties' => $this->fetchMapping($configModelRelated, $modelRelated),
                ];
            }
        }

        return $mapping;
    }
}
