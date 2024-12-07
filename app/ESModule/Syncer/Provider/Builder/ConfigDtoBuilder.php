<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Provider\Builder\Dto\ConfigDto;

class ConfigDtoBuilder
{
    public function __construct(
        private readonly ConnectionDtoBuilder $connectionDtoBuilder,
        private readonly DatabaseAdapter $databaseAdapter,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function build(DefaultConnection $connectionDefiner, array $indexDefiners): ConfigDto
    {
        $connectionDto = $this->connectionDtoBuilder->build($connectionDefiner, $indexDefiners);

        $tableNamesDetected = $this->databaseAdapter->fetchTableNames();
        $classNamesOrmDetected = $this->ormAdapter->fetchAllClassNames();

        $indexNamesDetected = [];
        foreach ($connectionDto->getIndexes() as $index) {
            $indexNamesDetected[] = $index->getName();
        }

        $tableNamesToClassNameOrmMapping = [];
        foreach ($tableNamesDetected as $tableName) {
            $classNameOrm = $classNamesOrmDetected[$tableName] ?? null;
            $tableNamesToClassNameOrmMapping[$tableName] = $classNameOrm;
        }

        $configDto = new ConfigDto(
            $connectionDto,
            $indexNamesDetected,
            $tableNamesDetected,
            array_values($classNamesOrmDetected),
            $tableNamesToClassNameOrmMapping
        );

        return $configDto;
    }
}
