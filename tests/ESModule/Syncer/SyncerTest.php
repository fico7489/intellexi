<?php

namespace Tests\ESModule\Syncer;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Syncer\Creator\CdcSyncable\CdcSyncableCreator;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\SearchEngine\SearchEngineDataClient;
use App\ESModule\Syncer\Syncer;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class SyncerTest extends TestCase
{
    public function testService()
    {
        $cdcDto = new CdcDto('test-database', 'test-table', CdcDto::TYPE_UPDATE, ['id' => 1], ['id'], []);
        $cdcDtoEvent = new CdcDtosEvent([$cdcDto]);
        $syncDto = new CdcSyncableDto('test-table', CdcDto::TYPE_UPDATE, ['id' => 1], ['id'], 1);
        $document = new DocumentDto('test-index', 1, ['id' => 3], DocumentDto::TYPE_UPSERT);

        $this->mock(CdcSyncableCreator::class, function (MockInterface $mock) use ($syncDto, $cdcDto) {
            $mock->allows('create')
                ->with($this->equalTo([$cdcDto]))
                ->andReturn([$syncDto])
                ->once();
        });

        $this->mock(DocumentCreator::class, function (MockInterface $mock) use ($document, $syncDto) {
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
