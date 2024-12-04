<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\SyncRow\Dto\SyncRowDto;
use App\ESModule\Syncer\Creator\SyncRow\Exception\GrouperException;
use App\ESModule\Syncer\Creator\SyncRow\SyncRowsCreator;

class ConvertDeleteTest extends TestCase
{
    public function testDeleteBasic()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDtos = [$cdcDto];

        $data = $this->cdcConverter->create($cdcDtos);

        $this->assertEquals(1, count($data['test-table']));

        /** @var CdcDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($cdcDto->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($cdcDto->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDbRow->getData());
    }

    public function testDeleteBasic2()
    {
        $cdcDto = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDto2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $cdcDtos = [$cdcDto, $cdcDto2];

        $this->mock(SyncRowsCreator::class, function ($mock) {
            $mock->shouldReceive('detectIdentifier')->andReturn(1)->once();
            $mock->shouldReceive('detectIdentifier')->andReturn(2)->once();
        })->makePartial();

        $data = app(SyncRowsCreator::class)->convert($cdcDtos);

        $this->assertEquals(2, count($data['test-table']));

        /** @var SyncRowDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($cdcDto->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($cdcDto->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals(1, $syncDbRow->getIdentifierValue());
        $this->assertEquals($cdcDto->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($cdcDto->getData(), $syncDbRow->getData());

        /** @var SyncRowDto $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($cdcDto2->getDatabaseName(), $syncDbRow2->getDatabaseName());
        $this->assertEquals($cdcDto2->getTableName(), $syncDbRow2->getTableName());
        $this->assertEquals(SyncRowDto::TYPE_DELETE, $syncDbRow2->getType());
        $this->assertEquals(2, $syncDbRow2->getIdentifierValue());
        $this->assertEquals($cdcDto2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($cdcDto2->getData(), $syncDbRow2->getData());
    }

    public function testDeleteAndUpsertExists()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_UPDATE);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->create($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var SyncRowDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow2->getDatabaseName(), $syncDbRow->getDatabaseName());
        $this->assertEquals($changedDbRow2->getTableName(), $syncDbRow->getTableName());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow->getData());
    }

    public function testDeleteExceptionAlreadyExists()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        try {
            $data = $this->cdcConverter->create($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: delete already deleted', $e->getMessage());
        }
    }
}
