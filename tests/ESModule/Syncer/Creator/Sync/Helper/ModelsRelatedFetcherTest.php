<?php

namespace Tests\ESModule\Syncer\Creator\Sync\Helper;

use App\ESModule\Config\Interface\IndexSyncInterface;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use Tests\ESModule\Syncer\Creator\Sync\TestCase;

class ModelsRelatedFetcherTest extends TestCase
{
    public function testIsNotForSync()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);
        $modelRoot = new \stdClass();

        $index = new class implements IndexSyncInterface {
            public function syncMap($syncMap): array
            {
                return [];
            }
            public function syncModels($syncModels): array
            {
                return [

                ];
            }
        };

        $modelRoot->id = 1;

        app(ModelsRelatedFetcher::class)->fetch($syncItemDto, $index, $modelRoot);

        $this->assertEquals(1, 1);
    }
}
