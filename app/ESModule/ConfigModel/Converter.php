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

        $mapping = $this->fetchMapping($configModel, new $className);

        return $mapping;
    }

    private function fetchMapping(array $data, Model $model): array
    {
        $mapping = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $propertyName = $value;

                $mapping[$propertyName] = ['type' => 'string'];
            } else {
                $relationName = $key;
                $configModel = $value;

                /** @var BelongsTo $relation */
                $relation = (new $model())->{$relationName}();

                $type = $relation instanceof BelongsTo ? 'object' : 'nested';
                $modelRelated = $relation->getRelated();

                $mapping[$relationName] = [
                    'type' => $type,
                    'properties' => $this->fetchMapping($value, $modelRelated),
                ];
            }
        }

        return $mapping;
    }
}
