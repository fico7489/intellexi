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
        private readonly SyncMapBuilder $syncMapBuilder,
        private readonly DatabaseAdapter $databaseAdapter,
        private readonly OrmAdapter $ormAdapter,
    ) {
    }

    public function build(DefaultConnection $connectionDefiner, array $indexDefiners): ConfigDto
    {
        $connectionDto = $this->connectionDtoBuilder->build($connectionDefiner, $indexDefiners);

        // TODO
        $databaseName = 'intellexi';

        $syncMap = $this->syncMapBuilder->buildSyncMapping($indexDefiners);

        $tableNamesDetected = $this->databaseAdapter->fetchTableNames();
        $classNamesOrm = $this->ormAdapter->fetchAllClassNamesOrm();
        $classNamesOrmDetected = array_values($classNamesOrm);

        $indexNamesDetected = [];
        foreach ($connectionDto->getIndexes() as $index) {
            $indexNamesDetected[] = $index->getName();
        }

        $tableNamesToIndexNamesMapping = [];
        foreach ($tableNamesDetected as $tableName) {
            foreach ($connectionDto->getIndexes() as $index) {
                $tableNameIndex = $this->ormAdapter->convertClassNameOrmToTableName($index->getClassName());

                if ($tableName === $tableNameIndex) {
                    $tableNamesToIndexNamesMapping[$tableName] = $index->getName();
                }
            }
        }

        $tableNamesToClassNameOrmMapping = [];
        foreach ($tableNamesDetected as $tableName) {
            $classNameOrm = $classNamesOrmDetected[$tableName] ?? null;
            $tableNamesToClassNameOrmMapping[$tableName] = $classNameOrm;
        }

        $tableNamesForIndex = [];
        $tableNamesToIndexNamesMappingFlipped = array_flip($tableNamesToIndexNamesMapping);
        foreach ($indexNamesDetected as $indexName) {
            if (isset($tableNamesToIndexNamesMappingFlipped[$indexName])) {
                $tableNamesForIndex[] = $tableNamesToIndexNamesMappingFlipped[$indexName];
            }
        }

        $tableNamesForSync = [];
        foreach ($syncMap as $tableName => $syncMapItem) {
            $tableNamesForSync[] = $tableName;
        }

        $configDto = new ConfigDto(
            $connectionDto,
            $databaseName,
            $syncMap,
            $tableNamesDetected,
            $indexNamesDetected,
            $classNamesOrmDetected,
            $tableNamesToIndexNamesMapping,
            $tableNamesToClassNameOrmMapping,
            $tableNamesForIndex,
            $tableNamesForSync,
        );

        return $configDto;
    }
}
