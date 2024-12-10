<?php

namespace Tests\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Converter\IndexModelsToDocumentsConverter;
use App\ESModule\Syncer\Creator\Document\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelCreator;
use Tests\TestCase;

class DocumentCreatorTest extends TestCase
{
    public function testService()
    {
        $cdcSyncableDto = new CdcSyncableDto('test-table', CdcSyncableDto::TYPE_UPSERT, [], [], 1, []);
        $indexModelDto = new IndexModelDto('test-index', 1, CdcSyncableDto::TYPE_UPSERT);
        $documentDto = new DocumentDto('test-index', 1, [], DocumentDto::TYPE_UPSERT);

        $this->mock(IndexModelCreator::class, function ($mock) use ($indexModelDto, $cdcSyncableDto) {
            $mock->shouldReceive('create')
                ->with([$cdcSyncableDto])
                ->andReturn([$indexModelDto])
                ->once();
        });

        $this->mock(IndexModelsToDocumentsConverter::class, function ($mock) use ($indexModelDto, $documentDto) {
            $mock->shouldReceive('convert')
                ->with([$indexModelDto])
                ->andReturn([$documentDto])
                ->once();
        });

        $documentDtos = app(DocumentCreator::class)->create([$cdcSyncableDto]);
    }
}
