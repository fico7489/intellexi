<?php

namespace Tests\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelFlattener;
use Tests\TestCase;

class IndexModelFlattenerTest extends TestCase
{
    public function testService()
    {
        $indexModelDtosGrouped['test-table'][1] = new IndexModelDto('test-index', 1, CdcSyncableDto::TYPE_UPSERT, new \stdClass());
        $indexModelDtosGrouped['test-table'][2] = new IndexModelDto('test-index', 2, CdcSyncableDto::TYPE_UPSERT, new \stdClass());

        $indexModelDtosFlattened = app(IndexModelFlattener::class)->flatten($indexModelDtosGrouped);

        $this->assertEquals([
            $indexModelDtosGrouped['test-table'][1],
            $indexModelDtosGrouped['test-table'][2],
        ], $indexModelDtosFlattened);
    }
}
