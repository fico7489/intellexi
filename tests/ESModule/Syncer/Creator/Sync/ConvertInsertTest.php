<?php

namespace Tests\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Creator\Sync\SyncItemCreator;

class ConvertInsertTest extends TestCase
{
    public function testInsertBasic()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
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

    public function testInsertBasic2()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mock(SyncItemCreator::class, function ($mock) {
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
            $mock->shouldReceive('detectIdentifier')->andReturn(2)->once();
        })->makePartial();

        $data = app(SyncItemCreator::class)->convert($cdcDtos);

        $this->assertEquals(2, count($data['test-table']));

        /** @var SyncDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($cdcDto->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($cdcDto->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDbRow->getData());

        /** @var SyncDto $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($cdcDto2->getDatabaseName(), $syncDbRow2->getDatabaseName());
        $this->assertEquals($cdcDto2->getTableName(), $syncDbRow2->getTableName());
        $this->assertEquals(SyncDto::TYPE_UPSERT, $syncDbRow2->getType());
        $this->assertEquals($cdcDto2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDbRow2->getData());
    }

    public function testInsertExceptionAlreadyExists()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $this->mock(SyncItemCreator::class, function ($mock) {
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
        })->makePartial();

        try {
            $data = app(SyncItemCreator::class)->convert($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: insert detected after insert, delete or update', $e->getMessage());
        }
    }
}
