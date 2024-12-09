<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Creator\CdcSyncable\Helper\IndexNamesForSyncFinder;
use Mockery\MockInterface;

class TestCase extends \Tests\TestCase
{
    protected CdcSyncableCreator $cdcConverter;

    protected function mockIndexNamesForSyncFinder($indexNamesForSync): void
    {
        $this->mock(IndexNamesForSyncFinder::class, function (MockInterface $mock) use ($indexNamesForSync) {
            $mock->allows('findIndexNamesForSync')->andReturn($indexNamesForSync);
        });
    }

    protected function mockDatabaseAdapter($mapping): void
    {
        $this->mock(DatabaseAdapter::class, function (MockInterface $mock) use ($mapping) {
            $mock->allows('fetchTableNamesToPrimaryKeysMapping')->andReturn($mapping);
        })->makePartial();
    }

    protected function createService(): CdcSyncableCreator
    {
        /** @var CdcSyncableCreator $syncItemCreator */
        $syncItemCreator = app(CdcSyncableCreator::class);

        return $syncItemCreator;
    }

    protected function createCdcDto(
        string $database = 'test-database',
        string $table = 'test-table',
        string $type = 'SET',
        array $data = ['id' => 1234, 'name' => '1234'],
        array $changedFields = ['name'],
    ): CdcDto {
        $changedDbRow = new CdcDto(
            $database,
            $table,
            $type,
            $data,
            $changedFields,
            [],
        );

        return $changedDbRow;
    }
}
