<?php

namespace Tests\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Creator\Sync\SyncItemCreator;

class CreatorInsertTest extends TestCase
{
    public function testInsertBasic()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDtos = [$cdcDto];

        $this->mockIdentifier(1);

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto->getType());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDto->getChangedFields());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
    }

    public function testInsertBasic2()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: ['id' => 3], changedFields: ['test']);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT, data: ['id' => 4], changedFields: ['test2']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mockIdentifier(1);
        $this->mockIdentifier(2);

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(2, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDto->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());
        $this->assertEquals(1, $syncDto->getIdentifierValue());

        $syncDto2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $syncDto2->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto2->getType());
        $this->assertEquals($cdcDto2->getChangedFields(), $syncDto2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDto2->getData());
        $this->assertEquals(2, $syncDto2->getIdentifierValue());
    }

    public function testInsertExceptionAlreadyExists()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT);

        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mockIdentifier(1);
        $this->mockIdentifier(1);

        $this->expectException(GrouperException::class);
        $this->expectExceptionMessage('Grouper: insert detected after insert, delete or update');
        $this->createService()->create($cdcDtos);
    }
}
