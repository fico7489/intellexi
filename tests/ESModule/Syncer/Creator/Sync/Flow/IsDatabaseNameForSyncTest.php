<?php

namespace Tests\ESModule\Syncer\Creator\Sync\Flow;

use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\Sync\TestCase;

class IsDatabaseNameForSyncTest extends TestCase
{
    public function testIsForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->shouldReceive('isTableNameForSync')->once();
        })->makePartial();

        $this->mock(DatabaseMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNamesToPrimaryKeysMapping')->andReturn([
                'test-table' => 'id'
            ]);
        })->makePartial();

        $data = $this->createService()->create($cdcDtos);
    }

    public function testIsNotForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(false);
            $mock->shouldReceive('isTableNameForSync')->never();
        })->makePartial();

        $data = $this->createService()->create($cdcDtos);
    }
}
