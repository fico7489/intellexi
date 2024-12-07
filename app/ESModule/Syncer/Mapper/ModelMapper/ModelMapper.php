<?php

namespace App\ESModule\Syncer\Mapper\ModelMapper;

class ModelMapper
{
    public function __construct(
        private readonly EloquentAdapter $ormAdapter,// TODO interface
    ) {
    }

    public function fetchAllClassNames(): array
    {
        return $this->ormAdapter->fetchAllClassNames();
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

    public function fetchModel(string $className, mixed $identifierValue): ?object
    {
        return $this->ormAdapter->fetchModel($className, $identifierValue);
    }

    public function fetchTableNameFromModel(object $model): string
    {
        return $this->ormAdapter->fetchTableNameFromModel($model);
    }

    public function fetchIdentifierValueFromModel(object $model): mixed
    {
        return $this->ormAdapter->fetchIdentifierValueFromModel($model);
    }
}
