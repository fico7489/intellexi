<?php

namespace Tests\ESModule\Syncer\Creator\Document\IndexModel;

use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\Document\Dto\IndexModelDto;
use App\ESModule\Syncer\Creator\Document\IndexModel\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelCreator;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelFlattener;
use App\ESModule\Syncer\Creator\Document\IndexModel\IndexModelItemCreator;
use Mockery\MockInterface;
use Tests\TestCase;

class IndexModelCreatorTest extends TestCase
{
    public function testService()
    {
        $cdcSyncableDto = new CdcSyncableDto('test-table', CdcSyncableDto::TYPE_UPSERT, [], [], 1, ['test-index']);
        $indexModelDto = new IndexModelDto('test-index', 1, CdcSyncableDto::TYPE_UPSERT, new \stdClass());
        $modelSource = new \stdClass();

        $this->mock(ModelSourceFetcher::class, function ($mock) use ($modelSource, $cdcSyncableDto) {
            $mock->shouldReceive('fetch')
                ->with($cdcSyncableDto)
                ->andReturn($modelSource)
                ->once();
        });

        $this->mock(IndexModelItemCreator::class, function (MockInterface $mock) use ($cdcSyncableDto, $modelSource, $indexModelDto) {
            $mock->expects('create')
                ->with([], $cdcSyncableDto, $modelSource, 'test-index')
                ->andReturns([
                    'test-table' => [
                        1 => $indexModelDto,
                    ],
                ]);
        });

        $this->mock(IndexModelFlattener::class, function (MockInterface $mock) use ($indexModelDto) {
            $mock->expects('flatten')
                ->with([
                    'test-table' => [
                        1 => $indexModelDto,
                    ],
                ])
                ->andReturns([$indexModelDto]);
        });

        $documentDtos = app(IndexModelCreator::class)->create([$cdcSyncableDto]);

        $this->assertCount(1, $documentDtos);
    }
}
