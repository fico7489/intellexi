<?php

namespace App\ESModule\Syncer;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ModelMapper
{
    public function fetchAllModelClassNames(): array
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
            $table = (new $className())->getTable();

            $mapping[$table] = $className;
        }

        return $mapping;
    }

    public function convertTableToClassName($table): string
    {
        $mapping = $this->fetchAllModelClassNames();

        return $mapping[$table];
    }

    public function convertClassNameToTable($className): string
    {
        $mapping = $this->fetchAllModelClassNames();

        return array_flip($mapping)[$className];
    }
}
