<?php

namespace Tests\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Creator\Sync\SyncItemCreator;

class CreatorUpdateTest extends TestCase
{
    public function testUpdateBasic()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $this->mockIdentifier(1);

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDto->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
    }

    public function testUpdateTwoSameRowDifferentData()
    {
        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 2, 'name' => 'test-2', 'name2' => 'test2-2'];

        $this->mockIdentifier(1);
        $this->mockIdentifier(1);

        $cdcDto = $this->createCdcDto( data: $data, changedFields: ['name2']);
        $cdcDto2 = $this->createCdcDto(data: $data2, changedFields: ['name3']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(1, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto2->getTableName(), $syncDto->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto->getType());
        $this->assertEquals(['name2', 'name3'], $syncDto->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDto->getData());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
    }

    public function testUpdateTwoDifferentRow()
    {
        $data = ['id' => 1, 'name' => 'test', 'name2' => 'test'];
        $data2 = ['id' => 2, 'name' => 'test2', 'name2' => 'test2'];

        $cdcDto = $this->createCdcDto(data: $data, changedFields: ['name2']);
        $cdcDto2 = $this->createCdcDto(data: $data2, changedFields: ['name3']);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mockIdentifier(1);
        $this->mockIdentifier(2);

        $data = $this->createService()->create($cdcDtos);

        $this->assertEquals(2, count($data));

        $syncDto = $data[0];
        $this->assertEquals($cdcDto->getTableName(), $syncDto->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto->getType());
        $this->assertEquals(1, $syncDto->getIdentifierValue());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDto->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDto->getData());

        $syncDto2 = $data[1];
        $this->assertEquals($cdcDto2->getTableName(), $syncDto2->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDto2->getType());
        $this->assertEquals(2, $syncDto2->getIdentifierValue());
        $this->assertEquals($cdcDto2->getChangedFields(), $syncDto2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDto2->getData());
    }

    public function testUpdateExceptionAfterDelete()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mockIdentifier(1);
        $this->mockIdentifier(1);

        $this->expectException(GrouperException::class);
        $this->expectExceptionMessage('Grouper: update detected after delete');
        $this->createService()->create($cdcDtos);
    }
}
