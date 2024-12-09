<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use App\ESModule\Syncer\Creator\CdcSyncable\Helper\IndexNamesForSyncFinder;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class UpdateMergeIndexTest extends TestCase
{
    public function testFeature()
    {
        $this->mockDatabaseAdapter(['test-table' => 'id']);

        $this->mock(IndexNamesForSyncFinder::class, function (MockInterface $mock) {
            $mock->allows('findIndexNamesForSync')->andReturn(['test'])->once();
            $mock->allows('findIndexNamesForSync')->andReturn(['test2'])->once();
        });

        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2-2'];

        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: $data, changedFields: ['name2']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: $data2, changedFields: ['name3']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(1, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto2->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals(['name2', 'name3'], $cdcSyncable->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $cdcSyncable->getData());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        $this->assertEquals(['test', 'test2'], $cdcSyncable->getIndexNamesForSync());
    }
}
