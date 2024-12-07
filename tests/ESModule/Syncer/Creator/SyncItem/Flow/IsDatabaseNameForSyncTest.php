<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Flow;

use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class IsDatabaseNameForSyncTest extends TestCase
{
    public function testIsForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->expects('isTableNameForSync')->once();
        })->makePartial();

        $this->createService()->create($cdcDtos);
    }

    public function testIsNotForSync()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(false);
            $mock->allows('isTableNameForSync')->never();
        })->makePartial();

        $this->createService()->create($cdcDtos);
    }
}
