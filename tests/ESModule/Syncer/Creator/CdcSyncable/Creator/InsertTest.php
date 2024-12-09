<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class InsertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockIndexNamesForSyncFinder(['test']);
        $this->mockDatabaseAdapter(['test-table' => 'id']);
    }

    public function testInsertOne()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: ['id' => 1]);
        $cdcDtos = [$cdcDto];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(1, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals($cdcDto->getData(), $cdcSyncable->getData());
        $this->assertEquals([], $cdcSyncable->getChangedFields());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        $this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());
    }

    public function testInsertTwo()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: ['id' => 1], changedFields: ['test']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: ['id' => 2], changedFields: ['test2']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertCount(2, $data);

        $cdcSyncable = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $cdcSyncable->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable->getType());
        $this->assertEquals([], $cdcSyncable->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $cdcSyncable->getData());
        $this->assertEquals(1, $cdcSyncable->getIdentifierValue());
        $this->assertEquals(['test'], $cdcSyncable->getIndexNamesForSync());

        $cdcSyncable2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $cdcSyncable2->getTableName());
        $this->assertEquals(CdcSyncableDto::TYPE_UPSERT, $cdcSyncable2->getType());
        $this->assertEquals([], $cdcSyncable2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $cdcSyncable2->getData());
        $this->assertEquals(2, $cdcSyncable2->getIdentifierValue());
        $this->assertEquals(['test'], $cdcSyncable2->getIndexNamesForSync());
    }

    public function testInsertExceptionAlreadyExists()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insert after insert, delete or update');
        $this->createService()->create($cdcDtos);
    }
}
