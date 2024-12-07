<?php

namespace App\ESModule\Syncer\Mapper\ModelMapper;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class EloquentAdapter
{
    public function fetchModel(string $className, mixed $identifierValue): ?Model
    {
        // MAKE sure that newest model is fetched

        return $className::find($identifierValue);
    }

    public function fetchTableNameFromModel(Model $model): string
    {
        return $model->getTable();
    }

    public function fetchIdentifierValueFromModel(Model $model): mixed
    {
        $identifierName = $model->getKeyName();

        return $model->{$identifierName};
    }

    public function fetchAllClassNames(): array
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                $class = sprintf('%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) && !$reflection->isAbstract();
                }

                return $valid;
            });

        $classNames = $models->values()->toArray();

        $mapping = [];
        foreach ($classNames as $className) {
            $tableName = (new $className())->getTable();

            $mapping[$tableName] = $className;
        }

        return $mapping;
    }
}
