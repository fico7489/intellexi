<?php

namespace Tests\ESModule\Syncer\Creator\Sync;

use Airalo\Crowdin\Observers\CrowdinTranslationObserver;
use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\SyncItemCreator;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\Mock;
use Mockery\MockInterface;

class TestCase extends \Tests\TestCase
{
    protected SyncItemCreator $cdcConverter;
    protected $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(SyncMapper::class, function ($mock) {
            $mock->allows('isTableNameForSync')->andReturn(true);

        });

        $this->mock(DatabaseMapper::class, function ($mock) {
            $mock->allows('fetchTableNamesToPrimaryKeysMapping')->andReturn([
                'test-table' => 'id'
            ]);
        })->makePartial();
    }

    protected function createService(): SyncItemCreator
    {
        /** @var SyncItemCreator $syncItemCreator */
        $syncItemCreator = app(SyncItemCreator::class);

        return $syncItemCreator;
    }

    protected function createCdcDto(
        string $database = 'test-database',
        string $table = 'test-table',
        string $type = CdcDto::TYPE_UPDATE,
        array  $data = ['id' => 1, 'name' => 'test2'],
        array  $changedFields = ['name'],
    ): CdcDto
    {
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
