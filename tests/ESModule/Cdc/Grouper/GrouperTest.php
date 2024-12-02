<?php

namespace Tests\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use App\ESModule\Cdc\Dto\SyncDbRow;
use App\ESModule\Cdc\Grouper\Grouper;
use Tests\TestCase;

class GrouperTest extends TestCase
{
    private Grouper $grouper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouper = app(Grouper::class);
    }

    public function testUpdateBasic()
    {
        $changedDbRow = $this->createChangedRow();
        $changedDbRows = [$changedDbRow];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(1, count($data));

        /** @var SyncDbRow $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(SyncDbRow::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals($changedDbRow->getChangedFields(), $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow->getData(), $syncDbRow->getData());
    }

    public function testUpdateTwoSameRowDifferentData()
    {
        $data = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2'];
        $data2 = ['id' => 1, 'name' => 'test-2', 'name2' => 'test2-2'];

        $changedDbRow = $this->createChangedRow(changedFields: ['name'], data: $data);
        $changedDbRow2 = $this->createChangedRow(changedFields: ['name2'], data: $data2);
        $changedDbRows = [$changedDbRow, $changedDbRow2];

        $data = $this->grouper->group($changedDbRows);

        $this->assertEquals(1, count($data));

        /** @var SyncDbRow $syncDbRow */
        $syncDbRow = $data['test-table'][1];

        $this->assertEquals($changedDbRow->getDatabase(), $syncDbRow->getDatabase());
        $this->assertEquals($changedDbRow->getTable(), $syncDbRow->getTable());
        $this->assertEquals(SyncDbRow::TYPE_UPSERT, $syncDbRow->getType());
        $this->assertEquals($changedDbRow->getIdentifier(), $syncDbRow->getIdentifier());
        $this->assertEquals(['name', 'name2'], $syncDbRow->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow->getData());
    }

    private function createChangedRow(
        string $database = 'test-database',
        string $table = 'test-table',
        string $type = ChangedDbRow::TYPE_UPDATE,
        mixed  $identifier = 1,
        array  $changedFields = ['name'],
        array  $data = ['id' => 1, 'name' => 'test2'],
    )
    {
        $changedDbRow = new ChangedDbRow(
            $database,
            $table,
            $type,
            $identifier,
            $changedFields,
            $data
        );

        return $changedDbRow;
    }
}
