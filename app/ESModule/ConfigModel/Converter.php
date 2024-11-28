<?php

namespace App\ESModule\ConfigModel;

use App\ESModule\Interface\IndexInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Converter
{
    //convert configModel to mapping
    public function convert(IndexInterface $index): array
    {
        $configModel = $index->getConfigModel();
        $className = $index->getClassName();

        return $this->fetchMapping($configModel, new $className);
    }

    private function fetchMapping(array $configModel, Model $model): array
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
