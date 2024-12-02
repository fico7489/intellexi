<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Exception\GrouperException;

class GrouperUpdateTest extends TestCase
{
    public function testUpdateBasic()
    {
        $changedDbRow = $this->createCdcDto();
        $changedDbRows = [$changedDbRow];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(ChangedRowGroupedDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());
    }

    public function testUpdateTwoSameRowDifferentData()
    {
        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2-2'];

        $changedDbRow = $this->createCdcDto(changedFields: ['name'], data: $data);
        $changedDbRow2 = $this->createCdcDto(changedFields: ['name2'], data: $data2);
        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(1, count($data));

        /* @var ChangedRowGroupedDto $syncDbRow */
        $this->assertEquals(1, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(ChangedRowGroupedDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals(['name', 'name2'], $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow->getData());
    }

    public function testUpdateTwoDifferentRow()
    {
        $data = ['id' => 1, 'name' => 'test', 'name2' => 'test'];
        $data2 = ['id' => 2, 'name' => 'test2', 'name2' => 'test2'];

        $changedDbRow = $this->createCdcDto(identifier: 1, changedFields: ['name'], data: $data);
        $changedDbRow2 = $this->createCdcDto(identifier: 2, changedFields: ['name'], data: $data2);
        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->cdcConverter->convert($changedDbRows);

        $this->assertEquals(2, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(ChangedRowGroupedDto::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow2->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow2->getTable());
        $this->assertEquals(ChangedRowGroupedDto::TYPE_UPSERT, $syncDbRow2->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow2->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow2->getData());
    }

    public function testUpdateExceptionAfterDelete()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_DELETE, identifier: 1);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_UPDATE, identifier: 1);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        try {
            $data = $this->cdcConverter->convert($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: update detected after delete', $e->getMessage());
        }
    }
}
