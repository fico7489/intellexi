<?php

namespace Tests\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\SyncDocument\SyncableDocumentsCreator;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncableItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class DocumentsCreateDocumentsTest extends TestCase
{
    public function testNotMatchedTable()
    {
        $syncItemDto = new SyncableItemDto('test_table', SyncableItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);

        $mock = $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table2' => [
                    'test-index' => [
                        'id',
                    ],
                ],
            ];

            // $mock->allows('create')->andReturn([])->once();
        });

        $service = app(SyncableDocumentsCreator::class);

        $mock2 = \Mockery::instanceMock($service, function (MockInterface $mock) {
            $mock->expects('testTwo')->times(1);
        })->makePartial();

        $mock2->create([]);
    }
}
