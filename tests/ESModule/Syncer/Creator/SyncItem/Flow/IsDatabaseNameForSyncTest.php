<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Flow;

use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class IsDatabaseNameForSyncTest extends TestCase
{
    public function testIsForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->expects('isTableNameForSync')->once();
        })->makePartial();

        $this->createService()->create($cdcDtos);
    }

    public function testIsNotForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(false);
            $mock->allows('isTableNameForSync')->never();
        })->makePartial();

        $this->createService()->create($cdcDtos);
    }
}
