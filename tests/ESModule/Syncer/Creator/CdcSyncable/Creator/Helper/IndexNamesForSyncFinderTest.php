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

        app(IndexNamesForSyncFinder::class)->findIndexNamesForSync($cdcDto);
    }

    public function testTableNameNotForSync()
    {
        $cdcDto = $this->createCdcDto();

        $mock = $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('getSyncMap')->andReturn([])->once();
        })->makePartial();

        $service = \Mockery::mock(
            IndexNamesForSyncFinder::class,
            [$mock]
        )->makePartial();

        $service->allows('shouldSync')->never();

        $service->findIndexNamesForSync($cdcDto);
    }

    public function testShouldSyncFalse()
    {
        $cdcDto = $this->createCdcDto();

        $mock = $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('getSyncMap')->andReturn([
                'test-table' => [
                    'test-index' => ['id'],
                ],
            ])->once();
        })->makePartial();

        $service = \Mockery::mock(
            IndexNamesForSyncFinder::class,
            [$mock]
        )->makePartial();

        $service->allows('shouldSync')->once()->andReturn(false);

        $indexNamesForSync = $service->findIndexNamesForSync($cdcDto);
        $this->assertCount(0, $indexNamesForSync);
    }

    public function testShouldSyncTrue()
    {
        $cdcDto = $this->createCdcDto();

        $mock = $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
            $mock->allows('getSyncMap')->andReturn([
                'test-table' => [
                    'test-index' => ['id'],
                ],
            ])->once();
        })->makePartial();

        $service = \Mockery::mock(
            IndexNamesForSyncFinder::class,
            [$mock]
        )->makePartial();

        $service->allows('shouldSync')->once()->andReturn(true);

        $indexNamesForSync = $service->findIndexNamesForSync($cdcDto);
        $this->assertCount(1, $indexNamesForSync);
    }
}
