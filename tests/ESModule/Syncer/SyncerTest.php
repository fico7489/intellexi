<?php

namespace Tests\ESModule\Syncer;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Syncer\Creator\SyncDocument\Dto\SyncableDocumentDto;
use App\ESModule\Syncer\Creator\SyncDocument\SyncableDocumentsCreator;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncableItemDto;
use App\ESModule\Syncer\Creator\SyncItem\SyncableItemsCreator;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;
use App\ESModule\Syncer\Syncer;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class SyncerTest extends TestCase
{
    public function testService()
    {
        $cdcDto = new CdcDto('test-database', 'test-table', CdcDto::TYPE_UPDATE, ['id' => 1], ['id'], []);
        $cdcDtoEvent = new CdcDtosEvent([$cdcDto]);
        $syncDto = new SyncableItemDto('test-table', CdcDto::TYPE_UPDATE, ['id' => 1], ['id'], 1);
        $document = new SyncableDocumentDto('test-index', 1, ['id' => 3], SyncableDocumentDto::TYPE_UPSERT);

        $this->mock(SyncableItemsCreator::class, function (MockInterface $mock) use ($syncDto, $cdcDto) {
            $mock->allows('create')
                ->with($this->equalTo([$cdcDto]))
                ->andReturn([$syncDto])
                ->once();
        });

        $this->mock(SyncableDocumentsCreator::class, function (MockInterface $mock) use ($document, $syncDto) {
            $mock
                ->allows('create')
                ->with($this->equalTo([$syncDto]))
                ->andReturn([$document])
                ->once();
        });

        $this->mock(SearchEngineDataClient::class, function (MockInterface $mock) use ($document) {
            $mock
                ->allows('syncDocuments')
                ->with($this->equalTo([$document]))
                ->once();
        });

        app(Syncer::class)->sync($cdcDtoEvent);
    }
}
