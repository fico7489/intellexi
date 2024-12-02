<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use App\ESModule\Syncer\CdcConverter\Exception\GrouperException;

class GrouperDeleteTest extends TestCase
{
    public function testDeleteBasic()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $changedDbRows = [$changedDbRow];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var CdcDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());
    }

    public function testDeleteBasic2()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_DELETE);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(2, count($data['test-table']));

        /** @var SyncRowDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());

        /** @var SyncRowDto $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow2->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow2->getTable());
        $this->assertEquals(SyncRowDto::TYPE_DELETE, $syncDbRow2->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow2->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow2->getData());
    }

    public function testDeleteAndUpsertExists()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_UPDATE);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_DELETE);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var SyncRowDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow->getTable());
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
            $data = $this->cdcConverter->convert($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: delete already deleted', $e->getMessage());
        }
    }
}
