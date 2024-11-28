<?php

namespace App\ESModule\ConfigModel;

use App\ESModule\Interface\IndexInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataConverter
{
    //convert configModel to data
    public function convertData(IndexInterface $index, mixed $entity): array
    {
        $configModel = $index->getConfigModel();
        $className = $index->getClassName();

        return $this->fetchData($configModel, $entity);
    }

    private function fetchData(array $configModel, $model): array
    {
        $data = [];
        foreach ($configModel as $key => $value) {
            if (is_int($key)) {
                $propertyName = $value;

                $data[$propertyName] = $model->{$value};
            } else {
                $relation = $model->{$key};

                if ($relation instanceof Model) {
                    $data[$key] = $this->fetchData($value, $relation);
                } elseif ($relation instanceof Collection) {
                    $data[$key] = [];
                    foreach ($relation as $item) {
                        $data[$key][] = $this->fetchData($value, $item);
                    }
                }
            }
        }

        return $data;
    }
}
