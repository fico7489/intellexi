<?php

namespace App\ESModule\Syncer\Mapper\ModelMapper;

use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ModelMapper
{
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

    public function convertTableNameToClassName($tableName): string
    {
        $mapping = $this->fetchAllClassNames();

        return $mapping[$tableName];
    }

    public function convertClassNameToTableName($className): string
    {
        $mapping = $this->fetchAllClassNames();

        return array_flip($mapping)[$className];
    }

    public function isClassNameModel($className): string
    {
        $mapping = $this->fetchAllClassNames();

        return isset(array_flip($mapping)[$className]);
    }

    public function isTableNameModel($tableName): string
    {
        $mapping = $this->fetchAllClassNames();

        return isset($mapping[$tableName]);
    }

    public function fetchModel(SyncItemDto $syncItemDto, string $className): ?object
    {
        // MAKE sure that newest model is fetched

        return $className::find($syncItemDto->getIdentifierValue());
    }

    public function fetchTableNameFromModel(Model $model): string
    {
        return $model->getTable();
    }

    public function fetchIdentifierValueFromModel(Model $model) : mixed
    {
        $identifierName = $model->getKeyName();

        return $model->{$identifierName};
    }
}
