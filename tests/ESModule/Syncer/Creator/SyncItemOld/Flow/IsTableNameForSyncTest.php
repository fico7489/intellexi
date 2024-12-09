<?php

namespace Tests\ESModule\Syncer\Creator\SyncItemOld\Flow;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class IsTableNameForSyncTest extends TestCase
{
    public function testIsForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
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

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('isTableNameForSync')->andReturn(false);
        })->makePartial();

        $data = $this->createService()->create($cdcDtos);
        $this->assertEquals(0, count($data));
    }
}
