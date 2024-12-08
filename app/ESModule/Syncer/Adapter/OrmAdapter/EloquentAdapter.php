<?php

namespace App\ESModule\Syncer\Adapter\OrmAdapter;

use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class EloquentAdapter
{
    public function fetchModel(SyncItemDto $syncItemDto, string $classNameOrm): ?Model
    {
        $tableName = $syncItemDto->getTableName();

        // return $classNameOrm::find($syncItemDto->getIdentifierValue());

        /** @var Model $instance */
        $instance = new $classNameOrm();
        $instance->forceFill($syncItemDto->getData());

        return $instance;
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

    public function fetchAllClassNamesOrm(): array
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

        $classNamesOrm = $models->values()->toArray();

        $mapping = [];
        foreach ($classNamesOrm as $classNameOrm) {
            $tableName = (new $classNameOrm())->getTable();

            $mapping[$tableName] = $classNameOrm;
        }

        return $mapping;
    }
}
