<?php

namespace App\ESModule\Syncer\Provider\Builder\Dto;

class ConfigDto
{
    public function __construct(
        private readonly ConnectionDto $connectionDto,
        private readonly string $databaseName,
        private readonly array $tableNamesDetected,
        private readonly array $indexNamesDetected,
        private readonly array $classNamesOrmDetected,
        private readonly array $tableNamesToIndexNamesMapping,
        private readonly array $tableNamesToClassNameOrmMapping,
    ) {
    }

    public function getConnectionDto(): ConnectionDto
    {
        return $this->connectionDto;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function getTableNamesDetected(): array
    {
        return $this->tableNamesDetected;
    }

    public function getIndexNamesDetected(): array
    {
        return $this->indexNamesDetected;
    }

    public function getClassNamesOrmDetected(): array
    {
        return $this->classNamesOrmDetected;
    }

    public function getTableNamesToIndexNamesMapping(): array
    {
        return $this->tableNamesToIndexNamesMapping;
    }

    public function getTableNamesToClassNameOrmMapping(): array
    {
        return $this->tableNamesToClassNameOrmMapping;
    }
}
