<?php

namespace App\ESModule\Syncer\Eloquent;

use App\ESModule\Config\ConfigFetcher;
use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Config\Sync\RelatedModelSync;
use App\ESModule\Config\Sync\RelatedTableSync;
use App\ESModule\Config\Sync\RootSync;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ModelMapper
{
    public function __construct(
        private readonly ConfigFetcher $configFetcher,
        // private readonly ModelMapper $modelMapper,
    ) {
    }

    public function isSyncDatabaseNameAndTableName($databaseName, $tableName): bool
    {
        $syncTableNames = $this->getSyncTableNames();

        return isset($syncTableNames[$databaseName][$tableName]);
    }

    public function getSyncTableNames(): array
    {
        $databaseMapping = $this->fetchDatabaseMapping();

        $syncTableNames = [];
        foreach ($databaseMapping as $databaseName => $data) {
            foreach ($data as $tableName => $items) {
                $syncTableNames[$databaseName][$tableName] = true;
            }
        }

        return $syncTableNames;
    }

    public function fetchDatabaseMapping(): array
    {
        $databaseMapping = [];

        $indexDefiners = $this->configFetcher->fetchIndexes();
        foreach ($indexDefiners as $indexDefiner) {
            $className = $indexDefiner->getClassName();

            $databaseName = $this->fetchDatabaseNameFromClassName($className);
            $indexName = $indexDefiner->getIndexName();

            $sync = $indexDefiner->getSync();
            foreach ($sync as $syncItem) {
                if ($syncItem instanceof RootSync) {
                    $tableName = $this->convertClassNameToTable($className);
                    $updatingFields = $syncItem->getUpdatingFields();
                    $fetchType = null;

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $fetchType, $updatingFields);
                }

                if ($syncItem instanceof RelatedModelSync) {
                    $className = $syncItem->getClassName();
                    $tableName = $this->convertClassNameToTable($className);
                    $fetchType = $syncItem->getFetchType();
                    $updatingFields = $syncItem->getUpdatingFields();

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $fetchType, $updatingFields);
                }

                if ($syncItem instanceof RelatedTableSync) {
                    $tableName = $syncItem->getTableName();
                    $updatingFields = $syncItem->getUpdatingFields();
                    $fetchType = $syncItem->getFetchType();

                    $databaseMapping = $this->addMapping($databaseMapping, $databaseName, $indexName, $tableName, $fetchType, $updatingFields);
                }
            }
        }

        return $databaseMapping;
    }

    private function addMapping(array $databaseMapping, $databaseName, $indexName, $tableName, $fetchType, $updatingFields): array
    {
        $databaseMapping[$databaseName][$tableName][] = [
            'index' => $this->detectIndexDefinerByName($indexName),
            'updatingFields' => $updatingFields,
            'fetchType' => $fetchType,
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
            $tableName = (new $className())->getTable();

            $mapping[$tableName] = $className;
        }

        return $mapping;
    }

    public function fetchTablePrimaryKeysMapping(): array
    {
        $tablesNames = collect(DB::connection()->select('show tables'))->map(function ($val) {
            foreach ($val as $key => $tbl) {
                return $tbl;
            }
        })->toArray();

        $tablePrimaryKeysMapping = [];
        foreach ($tablesNames as $tablesName) {
            $primaryKeyObjects = DB::connection()->select('SHOW KEYS FROM '.$tablesName." WHERE Key_name = 'PRIMARY'");

            $primaryKeys = [];
            foreach ($primaryKeyObjects as $primaryKeyObject) {
                $primaryKeys[] = $primaryKeyObject->Column_name;
            }

            $tablePrimaryKeysMapping[$tablesName] = $primaryKeys;
        }

        return $tablePrimaryKeysMapping;
    }

    public function convertTableNameToClassName($tableName): string
    {
        $mapping = $this->fetchAllClassNames();

        return $mapping[$tableName];
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

    public function detectIdentifierName(string $tableName): string|array
    {
        $className = $this->convertTableNameToClassName($tableName);

        /** @var Model $model */
        $model = (new $className());

        return $model->getKeyName();
    }

    public function detectIdentifierValue(string $tableName, array $data): string|array
    {
        $mapping = $this->fetchTablePrimaryKeysMapping();

        $identifierName = $mapping[$tableName];

        if (count($identifierName) > 1) {
            $identifierValue = [];
            foreach ($identifierName as $identifierNameItem) {
                $identifierValue[] = $data[$identifierNameItem];
            }

            return $identifierValue;
        }

        $identifierName = $identifierName[0];

        return $data[$identifierName];
    }
}
