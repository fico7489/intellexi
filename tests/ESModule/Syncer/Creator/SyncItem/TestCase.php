<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;

class TestCase extends \Tests\TestCase
{
    protected CdcSyncableCreator $cdcConverter;
    protected $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('isTableNameForSync')->andReturn(true);
        })->makePartial();

        $this->mock(DatabaseAdapter::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNamesToPrimaryKeysMapping')->andReturn([
                'test-table' => 'id',
            ]);
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
        string $type = CdcDto::TYPE_UPDATE,
        array $data = ['id' => 1, 'name' => 'test2'],
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
