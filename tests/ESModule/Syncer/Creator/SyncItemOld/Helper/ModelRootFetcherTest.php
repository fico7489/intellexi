<?php

namespace Tests\ESModule\Syncer\Creator\SyncItemOld\Helper;

use App\ESModule\Syncer\Adapter\OrmAdapter\OrmAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\SyncableDocument\Helper\ModelSourceFetcher;
use App\ESModule\Syncer\Provider\ConfigProvider;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class ModelRootFetcherTest extends TestCase
{
    public function testIsNotForSync()
    {
        $syncItemDto = new CdcSyncableDto('test-table', CdcSyncableDto::TYPE_UPSERT, [], [], 1);

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
        $syncItemDto = new CdcSyncableDto('test-table', CdcSyncableDto::TYPE_UPSERT, [], [], 1);

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
