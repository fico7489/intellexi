<?php

namespace Tests\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use App\ESModule\Cdc\Dto\SyncDbRow;
use App\ESModule\Cdc\Exception\GrouperException;
use App\ESModule\Cdc\Grouper\Grouper;

class GrouperInsertTest extends TestCase
{
    protected Grouper $grouper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouper = app(Grouper::class);
    }

    public function testInsertBasic()
    {
        $changedDbRow = $this->createChangedRow(type: ChangedDbRow::TYPE_INSERT);
        $changedDbRows = [$changedDbRow];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(1, count($data['test-table']));

        /** @var SyncDbRow $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(SyncDbRow::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());
    }

    public function testInsertBasic2()
    {
        $changedDbRow = $this->createChangedRow(type: ChangedDbRow::TYPE_INSERT, identifier: 1);
        $changedDbRow2 = $this->createChangedRow(type: ChangedDbRow::TYPE_INSERT, identifier: 2);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(2, count($data['test-table']));

        /** @var SyncDbRow $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(SyncDbRow::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());

        /** @var SyncDbRow $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow2->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow2->getTable());
        $this->assertEquals(SyncDbRow::TYPE_UPSERT, $syncDbRow2->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow2->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow2->getData());
    }

    public function testInsertExceptionAlreadyExists()
    {
        $changedDbRow = $this->createChangedRow(type: ChangedDbRow::TYPE_INSERT, identifier: 1);
        $changedDbRow2 = $this->createChangedRow(type: ChangedDbRow::TYPE_INSERT, identifier: 1);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        try {
            $data = $this->grouper->group($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: insert detected after insert, delete or update', $e->getMessage());
        }
    }
}
