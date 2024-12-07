<?php

namespace App\ESModule\Syncer\Provider\Builder\Dto;

class ConfigDto
{
    public function __construct(
        private readonly ConnectionDto $connectionDto,
        private readonly array         $tableNamesDetected,
        private readonly array         $classNamesOrmDetected,
        private readonly array         $tableNamesToClassNameOrmMapping,
    )
    {
    }

    public function getConnectionDto(): ConnectionDto
    {
        return $this->connectionDto;
    }

    public function getTableNamesDetected(): array
    {
        return $this->tableNamesDetected;
    }

    public function getClassNamesOrmDetected(): array
    {
        return $this->classNamesOrmDetected;
    }

    public function getTableNamesToClassNameOrmMapping(): array
    {
        return $this->tableNamesToClassNameOrmMapping;
    }
}
