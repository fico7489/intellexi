<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncDto;
use App\ESModule\Syncer\Creator\SyncRow\Exception\GrouperException;
use App\ESModule\Syncer\Creator\SyncRow\SyncRowsCreator;

class ConvertUpdateTest extends TestCase
{
    public function testUpdateBasic()
    {
        $cdcDto = $this->createCdcDto();
        $cdcDtos = [$cdcDto];

        $data = $this->cdcConverter->create($cdcDtos);

        $this->assertEquals(1, count($data['test-table']));

        /** @var SyncDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($cdcDto->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($cdcDto->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDbRow->getData());
    }

    public function testUpdateTwoSameRowDifferentData()
    {
        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2-2'];

        $changedDbRow = $this->createCdcDto(changedFields: ['name'], data: $data);
        $changedDbRow2 = $this->createCdcDto(changedFields: ['name2'], data: $data2);
        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->create($changedDbRows);

        $this->assertEquals(1, count($data));

        /* @var SyncDto $syncDbRow */
        $this->assertEquals(1, count($data['test-table']));

        /** @var SyncDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($changedDbRow->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals(['name', 'name2'], $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow->getData());
    }

    public function testUpdateTwoDifferentRow()
    {
        $data = ['id' => 1, 'name' => 'test', 'name2' => 'test'];
        $data2 = ['id' => 2, 'name' => 'test2', 'name2' => 'test2'];

        $cdcDto = $this->createCdcDto(changedFields: ['name'], data: $data);
        $cdcDto2 = $this->createCdcDto(changedFields: ['name'], data: $data2);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mock(SyncRowsCreator::class, function ($mock) {
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
            $mock->shouldReceive('detectIdentifier')->andReturn(2)->once();
        })->makePartial();

        $data = app(SyncRowsCreator::class)->convert($cdcDtos);

        $this->assertEquals(2, count($data['test-table']));

        /** @var SyncDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($cdcDto->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($cdcDto->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals(1, $syncDbRow->getIdentifierValue());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDbRow->getData());

        /** @var SyncDto $syncDbRow */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($cdcDto2->getDatabaseName(), $syncDbRow2->getDatabase());
        $this->assertEquals($cdcDto2->getTableName(), $syncDbRow2->getTable());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow2->getType());
        $this->assertEquals(2, $syncDbRow2->getIdentifier());
        $this->assertEquals($cdcDto2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDbRow2->getData());
    }

    public function testUpdateExceptionAfterDelete()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mock(SyncRowsCreator::class, function ($mock) {
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
        })->makePartial();

        try {
            $data = app(SyncRowsCreator::class)->convert($cdcDtos);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: update detected after delete', $e->getMessage());
        }
    }
}
