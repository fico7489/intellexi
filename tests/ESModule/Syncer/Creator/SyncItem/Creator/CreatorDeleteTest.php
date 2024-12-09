<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Creator;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class CreatorDeleteTest extends TestCase
{
    public function testDeleteBasic()
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
    }

    public function testDeleteBasic2()
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

        $syncDto2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $syncDto2->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDto2->getType());
        $this->assertEquals($cdcDto2->getData(), $syncDto2->getData());
        $this->assertEquals([], $syncDto2->getChangedFields());
        $this->assertEquals(2, $syncDto2->getIdentifierValue());
    }

    public function testDeleteAndUpsertExists()
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
    }

    public function testDeleteExceptionAlreadyExists()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Grouper: delete already deleted');
        $this->createService()->create($cdcDtos);
    }
}
