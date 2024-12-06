<?php

namespace Tests\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class DocumentsCreateDocumentsTest extends TestCase
{
    public function testNotMatchedTable()
    {
        $syncItemDto = new SyncItemDto('test_table', SyncItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);

        $mock = $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table2' => [
                    'test-index' => [
                        'id',
                    ],
                ],
            ];

            $mock->allows('create')->andReturn([])->once();
        });

        $this->mock(DocumentsCreator::class, function ($mock) {
            $mock->shouldReceive('testTwo')->times(1);
        })->makePartial();

        $service = app(DocumentsCreator::class);

        $service->create([]);
    }
}
