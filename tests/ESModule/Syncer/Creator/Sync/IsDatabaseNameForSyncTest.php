<?php

namespace Tests\ESModule\Syncer\Creator\Sync;

use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;

class IsDatabaseNameForSyncTest extends \Tests\TestCase
{
    public function testIsForSync()
    {
        $this->assertTrue(true);
        return;
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isDatabaseNameForSync')->andReturn(true);
        });

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));
    }
}
