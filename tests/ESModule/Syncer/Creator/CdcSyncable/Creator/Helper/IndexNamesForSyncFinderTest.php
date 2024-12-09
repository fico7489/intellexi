<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator\Helper;

use App\ESModule\Syncer\Creator\CdcSyncable\Helper\IndexNamesForSyncFinder;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class IndexNamesForSyncFinderTest extends TestCase
{
    public function testDatabaseNameNotForSync()
    {
        $cdcDto = $this->createCdcDto();

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(false);
            $mock->allows('getSyncMap')->never();
        })->makePartial();

        $this->createServiceInternal()->findIndexNamesForSync($cdcDto);
    }

    public function testTableNameNotForSync()
    {
        $cdcDto = $this->createCdcDto();

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('getSyncMap')->andReturn([])->once();
        })->makePartial();

        $this->createServiceInternal()->findIndexNamesForSync($cdcDto);
    }

    private function createServiceInternal() : IndexNamesForSyncFinder
    {
        return app(IndexNamesForSyncFinder::class);
    }
}
