<?php

namespace App\ESModule\ConfigModel;

use App\ESModule\Interface\IndexInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Converter
{
    //convert configModel to mapping
    public function convertMapping(IndexInterface $index): array
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

    //convert configModel to mapping
    public function convertData(IndexInterface $index, mixed $entity): array
    {
        $configModel = $index->getConfigModel();
        $className = $index->getClassName();

        return $this->fetchData($configModel, $entity);
    }

    private function fetchData(array $data, $model): array
    {
        $modelData = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $modelData[$value] = $model->{$value};
            } else {
                $relation = $model->{$key};

                if ($relation instanceof Model) {
                    $modelData[$key] = $this->fetchData($value, $relation);
                } elseif ($relation instanceof Collection) {
                    $modelData[$key] = [];
                    foreach ($relation as $item) {
                        $modelData[$key][] = $this->fetchData($value, $item);
                    }
                }
            }
        }

        return $modelData;
    }
}
