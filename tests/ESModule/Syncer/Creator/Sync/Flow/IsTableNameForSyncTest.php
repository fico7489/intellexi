<?php

namespace Tests\ESModule\Syncer\Creator\Sync\Flow;

use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\Sync\TestCase;

class IsTableNameForSyncTest extends TestCase
{
    public function testIsForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('isTableNameForSync')->andReturn(true);
        })->makePartial();

        $data = $this->createService()->create($cdcDtos);
        $this->assertEquals(1, count($data));
    }

    public function testIsNotForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('isTableNameForSync')->andReturn(false);
        })->makePartial();

        $data = $this->createService()->create($cdcDtos);
        $this->assertEquals(0, count($data));
    }
}
