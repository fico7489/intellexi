<?php

namespace Tests\ESModule\Syncer\Creator\CdcSyncable\Creator;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use Tests\ESModule\Syncer\Creator\CdcSyncable\TestCase;

class CreatorDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockIndexNamesForSyncFinder(['test']);
    }

    public function testDeleteOne()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDtos = [$cdcDto];

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDto->getType());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());
        $this->assertEquals([], $syncDto->getChangedFields());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
        $this->assertEquals(['test'], $syncDto->getIndexNamesForSync());
    }

    public function testDeleteTwo()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE, data: ['id' => 1], changedFields: ['test']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE, data: ['id' => 2], changedFields: ['test2']);
        $cdcDtos = [$cdcDto, $cdcDto2];
        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(2, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDto->getType());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());
        $this->assertEquals([], $syncDto->getChangedFields());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
        $this->assertEquals(['test'], $syncDto->getIndexNamesForSync());

        $syncDto2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $syncDto2->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDto2->getType());
        $this->assertEquals($cdcDto2->getData(), $syncDto2->getData());
        $this->assertEquals([], $syncDto2->getChangedFields());
        $this->assertEquals(2, $syncDto2->getIdentifierValue());
        $this->assertEquals(['test'], $syncDto2->getIndexNamesForSync());
    }

    public function testDeleteAndUpsert()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, data: ['id' => 1]);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE, data: ['id' => 1]);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDto->getType());
        $this->assertEquals($cdcDto2->getData(), $syncDto->getData());
        $this->assertEquals([], $syncDto->getChangedFields());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
        $this->assertEquals(['test'], $syncDto->getIndexNamesForSync());
    }

    public function testDeleteAlreadyExists()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Delete already added');
        $this->createService()->create($cdcDtos);
    }
}
