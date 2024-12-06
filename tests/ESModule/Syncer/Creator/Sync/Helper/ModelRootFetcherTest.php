<?php

namespace Tests\ESModule\Syncer\Creator\Sync\Helper;

use App\ESModule\Syncer\Creator\Document\Helper\ModelRootFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\Sync\TestCase;

class ModelRootFetcherTest extends TestCase
{
    public function testIsForSync()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $mock->allows('isTableNameForIndex')->andReturn(false);
        });

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('convertTableNameToClassName')->never();
        });

        app(ModelRootFetcher::class)->fetch($syncItemDto);
    }


}
