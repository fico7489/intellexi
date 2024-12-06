<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Helper;

use App\ESModule\Config\Interface\IndexSyncInterface;
use App\ESModule\Syncer\Creator\Document\ModelsRelated\ModelsRelatedFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class ModelsRelatedFetcherTest extends TestCase
{
    public function testEmpty()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);
        $modelRoot = new \stdClass();
        $modelRoot->id = 1;
        $index = $this->createIndexSyncInterface([]);

        $modelsRelated = app(ModelsRelatedFetcher::class)->fetch($syncItemDto, $index, $modelRoot);

        $this->assertEquals(0, count($modelsRelated));
    }

    public function testOnlyTable()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);
        $modelRoot = new class {
            public $id = 1;
        };

        $object = new class {
            public $id = 1;
        };

        $index = $this->createIndexSyncInterface([
            'test-table' => function ($model, SyncItemDto $syncItemDto, array $relatedModels) use ($object) {
                return [$object];
            },
        ]);

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('isTableNameModel')->with($this->equalTo('test-table'))->andReturn(false)->once();
        });

        $modelsRelated = app(ModelsRelatedFetcher::class)->fetch($syncItemDto, $index, $modelRoot);

        $this->assertEquals(1, count($modelsRelated));
        $this->assertEquals($object, $modelsRelated[0]);
    }

    public function testOnlyModel()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);
        $modelRoot = new class {
            public $id = 1;
        };

        $object = new class {
            public $id = 1;
        };

        $index = $this->createIndexSyncInterface([
            $object::class => function ($model, SyncItemDto $syncItemDto, array $relatedModels) use ($object) {
                return [$object];
            },
        ]);

        $this->mock(ModelMapper::class, function (MockInterface $mock) use ($object) {
            $mock->allows('isTableNameModel')->with($this->equalTo('test-table'))->andReturn(true)->once();
            $mock->allows('convertTableNameToClassName')->with($this->equalTo('test-table'))->andReturn($object::class)->once();
        });

        $modelsRelated = app(ModelsRelatedFetcher::class)->fetch($syncItemDto, $index, $modelRoot);

        $this->assertEquals(1, count($modelsRelated));
        $this->assertEquals($object, $modelsRelated[0]);
    }

    public function testCombineTableAndModel()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);
        $modelRoot = new class {
            public $id = 1;
        };

        $object = new class {
            public $id = 1;
        };
        $object2 = new class {
            public $id = 1;
        };

        $index = $this->createIndexSyncInterface([
            'test-table' => function ($model, SyncItemDto $syncItemDto, array $relatedModels) use ($object) {
                return array_merge([$object], $relatedModels);
            },
            $object::class => function ($model, SyncItemDto $syncItemDto, array $relatedModels) use ($object2) {
                return array_merge([$object2], $relatedModels);
            },
        ]);

        $this->mock(ModelMapper::class, function (MockInterface $mock) use ($object) {
            $mock->allows('isTableNameModel')->with($this->equalTo('test-table'))->andReturn(true)->once();
            $mock->allows('convertTableNameToClassName')->with($this->equalTo('test-table'))->andReturn($object::class)->once();
        });

        $modelsRelated = app(ModelsRelatedFetcher::class)->fetch($syncItemDto, $index, $modelRoot);

        $this->assertEquals(2, count($modelsRelated));
        $this->assertEquals($object, $modelsRelated[0]);
        $this->assertEquals($object2, $modelsRelated[1]);
    }

    private function createIndexSyncInterface(array $syncModelsCurrent): IndexSyncInterface
    {
        $indexSyncInterface = new class implements IndexSyncInterface {
            public array $syncModelsCurrent = [];

            public function syncMap($syncMap): array
            {
                return [];
            }

            public function syncModels($syncModels): array
            {
                return $this->syncModelsCurrent;
            }
        };

        $indexSyncInterface->syncModelsCurrent = $syncModelsCurrent;

        return $indexSyncInterface;
    }
}
