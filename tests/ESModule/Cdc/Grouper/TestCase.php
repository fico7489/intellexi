<?php

namespace Tests\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use App\ESModule\Cdc\Grouper\Grouper;

class TestCase extends \Tests\TestCase
{
    protected Grouper $grouper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouper = app(Grouper::class);
    }

    protected function createChangedRow(
        string $database = 'test-database',
        string $table = 'test-table',
        string $type = ChangedDbRow::TYPE_UPDATE,
        mixed $identifier = 1,
        array $changedFields = ['name'],
        array $data = ['id' => 1, 'name' => 'test2'],
    ) {
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
