<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\SyncRow\SyncRowsCreator;

class TestCase extends \Tests\TestCase
{
    protected SyncRowsCreator $cdcConverter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cdcConverter = app(SyncRowsCreator::class);
    }

    protected function createCdcDto(
        string $database = 'test-database',
        string $table = 'test-table',
        string $type = CdcDto::TYPE_UPDATE,
        array $changedFields = ['name'],
        array $data = ['id' => 1, 'name' => 'test2'],
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
