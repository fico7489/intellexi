<?php

namespace Tests\ESModule\Syncer\CdcConverter;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Exception\GrouperException;
use App\ESModule\Cdc\Grouper\Grouper;

class GrouperInsertTest extends TestCase
{
    protected Grouper $cdcConverter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cdcConverter = app(Grouper::class);
    }

    public function testInsertBasic()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_INSERT);
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

    public function testInsertBasic2()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_INSERT, identifier: 1);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT, identifier: 2);

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

        /** @var ChangedRowGroupedDto $syncDbRow2 */
        $syncDbRow2 = $data['test-table'][2];

        $this->assertEquals($changedDbRow2->getDatabase(), $syncDbRow2->getDatabase());
        $this->assertEquals($changedDbRow2->getTable(), $syncDbRow2->getTable());
        $this->assertEquals(ChangedRowGroupedDto::TYPE_UPSERT, $syncDbRow2->getType());
        $this->assertEquals($changedDbRow2->getIdentifier(), $syncDbRow2->getIdentifier());
        $this->assertEquals($changedDbRow2->getChangedFields(), $syncDbRow2->getChangedFields());
        $this->assertEquals($changedDbRow2->getData(), $syncDbRow2->getData());
    }

    public function testInsertExceptionAlreadyExists()
    {
        $changedDbRow = $this->createCdcDto(type: CdcDto::TYPE_INSERT, identifier: 1);
        $changedDbRow2 = $this->createCdcDto(type: CdcDto::TYPE_INSERT, identifier: 1);

        $changedDbRows = [$changedDbRow, $changedDbRow2];

        try {
            $data = $this->cdcConverter->convert($changedDbRows);
        } catch (GrouperException $e) {
            $this->assertEquals('Grouper: insert detected after insert, delete or update', $e->getMessage());
        }
    }
}
