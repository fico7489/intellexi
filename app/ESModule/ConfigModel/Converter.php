<?php

namespace App\ESModule\ConfigModel;

use App\ESModule\Interface\IndexInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Converter{
    //convert configModel to mapping
    public function convert(IndexInterface $index) :array
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
                $mapping[$value] = ['type' => 'string'];
            } else {
                /** @var BelongsTo $relation */
                $relation = (new $model())->{$key}();

                $type = $relation instanceof BelongsTo ? 'object' : 'nested';
                $related = $relation->getRelated();

                $mapping[$key] = [
                    'type' => $type,
                    'properties' => $this->fetchMapping($value, $related),
                ];
            }
        }

        return $mapping;
    }
}
