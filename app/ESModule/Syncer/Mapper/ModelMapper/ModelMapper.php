<?php

namespace App\ESModule\Syncer\Mapper\ModelMapper;

use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ModelMapper
{
    public function __construct(
        private readonly DatabaseMapper $databaseMapper,
    ) {
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

    public function detectIdentifierValue(string $tableName, array $data): string|array
    {
        $mapping = $this->databaseMapper->fetchTableNamesToPrimaryKeysMapping();

        $identifierName = $mapping[$tableName];

        $identifierValue = $data[$identifierName];

        return $identifierValue;
    }

    public function detectIdentifierValue2(Model $model): mixed
    {
        // TODO
        return $model->id;
    }

    public function fetchModel(string $className, SyncDto $syncDto): ?Model
    {
        // MAKE sure that newest model is fetched

        return $className::find($syncDto->getIdentifierValue());
    }
}
