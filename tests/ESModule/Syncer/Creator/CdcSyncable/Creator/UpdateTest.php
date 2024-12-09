<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class UpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockIndexNamesForSyncFinder(['test']);
        $this->mockDatabaseAdapter(['test-table' => 'id']);
    }

    public function testUpdateOne()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: ['id' => 1]);
        $cdcDtos = [$cdcDto];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(1, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $cdcSyncable->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $cdcSyncable->getData());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        $this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());
    }

    public function testUpdateTwoSameRowDifferentData()
    {
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
        $this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());
    }

    public function testUpdateTwoDifferentRow()
    {
        $data = ['id' => 1, 'name' => 'test', 'name2' => 'test'];
        $data2 = ['id' => 2, 'name' => 'test2', 'name2' => 'test2'];

        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: $data, changedFields: ['name2']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: $data2, changedFields: ['name3']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(2, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        $this->assertEquals($cdcDto->getChangedFields(), $cdcSyncable->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $cdcSyncable->getData());
        $this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());

        $cdcSyncable2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $cdcSyncable2->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable2->getType());
        $this->assertEquals(2, $cdcSyncable2->getIdentifierValue());
        $this->assertEquals($cdcDto2->getChangedFields(), $cdcSyncable2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $cdcSyncable2->getData());
        $this->assertEquals(['test'], $cdcSyncable2->getIndexNamesForSync());
    }

    public function testUpdateAfterInsert()
    {
        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2-2'];

        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: $data, changedFields: ['name2']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: $data2, changedFields: ['name3']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(1, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals([], $cdcSyncable->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $cdcSyncable->getData());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        //$this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());
    }

    public function testUpdatefterDelete()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Update after delete');
        $this->createService()->create($cdcDtos);
    }
}
