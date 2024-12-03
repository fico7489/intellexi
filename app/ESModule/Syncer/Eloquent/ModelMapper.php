<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\Related\SyncRelationDto;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ModelMapper
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        // private readonly ModelMapper $modelMapper,
    ) {
    }

    public function syncForDatabaseAndTableName($databaseName, $tableName): bool
    {
        $databaseMapping = $this->fetchDatabaseMapping();

        return isset($databaseMapping[$databaseName][$tableName]);
    }

    public function fetchDatabaseMapping(): array
    {
        $databaseMapping = [];

        $indexDefiners = $this->configFetcher->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            $className = $indexDefiner->getClassName();
            $databaseName = $this->fetchDatabaseNameFromClassName($className);
            $tableName = $this->convertClassNameToTable($className);
            $syncRelations = $indexDefiner->getSyncRelations();
            $indexName = $indexDefiner->getIndexName();

            $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $tableName, $indexDefiner->getIndexName(), null, $indexDefiner->getUpdatingFields());

            foreach ($syncRelations as $syncRelationDto) {
                /** @var SyncRelationDto $syncRelationDto */
                $className = $syncRelationDto->getClassName();
                $relation = $syncRelationDto->getRelation();
                $updatingFields = $syncRelationDto->getUpdatingFields();

                $tableNameRelated = $this->convertClassNameToTable($className);

                $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $tableNameRelated, $indexName, $relation, $updatingFields);
            }
        }

        return $databaseMapping;
    }

    private function addMapping(array $databaseMapping, $databaseName, $tableNameRelated, $indexName, $relation, $updatingFields): array
    {
        $databaseMapping[$databaseName][$tableNameRelated][] = [
            'index' => $this->detectIndexDefinerByName($indexName),
            'relation' => $relation,
            'updatingFields' => $updatingFields,
        ];

        return $databaseMapping;
    }

    public function detectIndexDefinerByName(string $indexName): ?IndexDefinerModelInterface
    {
        $indexDefiners = $this->configFetcher->fetchIndexes();

        foreach ($indexDefiners as $indexDefiner) {
            if ($indexDefiner->getIndexName() === $indexName) {
                return $indexDefiner;
            }
        }

        return null;
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
            $table = (new $className())->getTable();

            $mapping[$table] = $className;
        }

        return $mapping;
    }

    public function convertTableToClassName($table): string
    {
        $mapping = $this->fetchAllClassNames();

        return $mapping[$table];
    }

    public function convertClassNameToTable($className): string
    {
        $mapping = $this->fetchAllClassNames();

        return array_flip($mapping)[$className];
    }

    public function fetchDatabaseNameFromClassName($className): string
    {
        /** @var Model $model */
        $model = (new $className());

        return $model->getConnection()->getDatabaseName();
    }
}
