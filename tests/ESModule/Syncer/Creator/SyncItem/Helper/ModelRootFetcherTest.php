<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\Document\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class ModelRootFetcherTest extends TestCase
{
    public function testIsNotForSync()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isTableNameForIndex')->andReturn(false);
        });

        $this->mock(OrmAdapter::class, function (MockInterface $mock) {
            $mock->allows('convertTableNameToClassName')->never();
        });

        app(ModelSourceFetcher::class)->fetch($syncItemDto);
    }

    public function testIsForSync()
    {
        $syncItemDto = new SyncItemDto('test-table', SyncItemDto::TYPE_UPSERT, [], [], 1);

        $this->mock(ConfigProvider::class, function (MockInterface $mock) {
            $mock->allows('isTableNameForIndex')->andReturn(true);
        });

        $className = 'App\Models\User';
        $model = new \stdClass();
        $model->id = 1;

        $this->mock(OrmAdapter::class, function (MockInterface $mock) use ($model, $syncItemDto, $className) {
            $mock->allows('convertTableNameToClassName')
                ->with($this->equalTo('test-table'))
                ->andReturn($className)
                ->once();

            $mock->allows('fetchModel')
                ->with($this->equalTo($syncItemDto), $this->equalTo($className))
                ->andReturn($model)
                ->once();
        });

        $modelFetched = app(ModelSourceFetcher::class)->fetch($syncItemDto);
        $this->assertEquals($model, $modelFetched);
    }
}
