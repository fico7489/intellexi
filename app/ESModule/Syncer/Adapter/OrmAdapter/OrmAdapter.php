<?php

namespace App\ESModule\Syncer\Adapter\OrmAdapter;

class OrmAdapter
{
    public function __construct(
        private readonly EloquentAdapter $ormAdapter,// TODO interface
    ) {
    }

    public function fetchAllClassNamesOrm(): array
    {
        return $this->ormAdapter->fetchAllClassNamesOrm();
    }

    public function convertTableNameToClassNameOrm($tableName): ?string
    {
        $mapping = $this->fetchAllClassNamesOrm();

        return $mapping[$tableName] ?? null;
    }

    public function convertClassNameOrmToTableName($classNameOrm): string
    {
        $mapping = $this->fetchAllClassNamesOrm();

        return array_flip($mapping)[$classNameOrm];
    }

    public function isClassNameOrm($classNameOrm): string
    {
        $mapping = $this->fetchAllClassNamesOrm();

        return isset(array_flip($mapping)[$classNameOrm]);
    }

    public function isTableNameOrm($tableName): string
    {
        $mapping = $this->fetchAllClassNamesOrm();

        return isset($mapping[$tableName]);
    }

    public function fetchModel(string $classNameOrm, mixed $identifierValue): ?object
    {
        return $this->ormAdapter->fetchModel($classNameOrm, $identifierValue);
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
