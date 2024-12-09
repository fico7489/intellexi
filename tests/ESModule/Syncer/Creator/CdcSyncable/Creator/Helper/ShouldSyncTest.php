<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator\Helper;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Helper\IndexNamesForSyncFinder;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class ShouldSyncTest extends TestCase
{
    public function testDelete()
    {
        $status = app(IndexNamesForSyncFinder::class)->shouldSync(CdcDto::TYPE_DELETE, ['id'], ['name']);
        $this->assertTrue($status);
    }

    public function testInsert()
    {
        $status = app(IndexNamesForSyncFinder::class)->shouldSync(CdcDto::TYPE_INSERT, ['id'], ['name']);
        $this->assertTrue($status);
    }

    public function testUpdateFalse()
    {
        $status = app(IndexNamesForSyncFinder::class)->shouldSync(CdcDto::TYPE_UPDATE, ['id'], ['name']);
        $this->assertFalse($status);
    }

    public function testUpdateTrue()
    {
        $status = app(IndexNamesForSyncFinder::class)->shouldSync(CdcDto::TYPE_UPDATE, ['id'], ['id', 'name']);
        $this->assertTrue($status);
    }
}
