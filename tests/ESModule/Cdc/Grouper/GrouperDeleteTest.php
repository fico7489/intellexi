<?php

namespace Tests\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Exception\GrouperException;
use App\ESModule\Cdc\Grouper\Grouper;

class GrouperDeleteTest extends TestCase
{
    protected Grouper $grouper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouper = app(Grouper::class);
    }

    public function testDeleteBasic()
    {
        $changedDbRow = $this->createChangedRow(type: CdcDto::TYPE_DELETE);
        $changedDbRows = [$changedDbRow];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());
    }

    public function testDeleteBasic2()
    {
        $changedDbRow = $this->createChangedRow(type: CdcDto::TYPE_DELETE, identifier: 1);
        $changedDbRow2 = $this->createChangedRow(type: CdcDto::TYPE_DELETE, identifier: 2);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(2, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());

        /** @var ChangedRowGroupedDto $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow2->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow2->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow2->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow2->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow2->getData());
    }

    public function testDeleteAndUpsertExists()
    {
        $changedDbRow = $this->createChangedRow(type: CdcDto::TYPE_UPDATE);
        $changedDbRow2 = $this->createChangedRow(type: CdcDto::TYPE_DELETE);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var ChangedRowGroupedDto $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow->getTable());
        $this->assertEquals(CdcDto::TYPE_DELETE, $syncDbRow->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow->getData());
    }

    public function testDeleteExceptionAlreadyExists()
    {
        $changedDbRow = $this->createChangedRow(type: CdcDto::TYPE_DELETE, identifier: 1);
        $changedDbRow2 = $this->createChangedRow(type: CdcDto::TYPE_DELETE, identifier: 1);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        try {
            $data = $this->grouper->group($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: delete already deleted', $e->getMessage());
        }
    }
}
