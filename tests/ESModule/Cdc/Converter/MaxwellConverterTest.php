<?php

namespace Tests\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Dto\ChangedRowDto;
use Tests\TestCase;

class MaxwellConverterTest extends TestCase
{
    public function testInsert()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"insert","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}}';

        $changedDbRow = app(MaxwellConverter::class)->convert($payload);

        $this->assertEquals(
            new ChangedRowDto(
                'test-db',
                'test-table',
                'insert',
                1,
                [],
                [
                    'id' => 1,
                    'name' => 'test-name',
                ]
            ),
            $changedDbRow,
        );
    }

    public function testDelete()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"delete","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}}';

        $changedDbRow = app(MaxwellConverter::class)->convert($payload);

        $this->assertEquals(
            new ChangedRowDto(
                'test-db',
                'test-table',
                'delete',
                1,
                [],
                [
                    'id' => 1,
                    'name' => 'test-name',
                ]
            ),
            $changedDbRow,
        );
    }

    public function testIUpdate()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"update","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}, "old" : {"name" : "test-name-old"}}';

        $changedDbRow = app(MaxwellConverter::class)->convert($payload);

        $this->assertEquals(
            new ChangedRowDto(
                'test-db',
                'test-table',
                'update',
                1,
                [
                    'name',
                ],
                [
                    'id' => 1,
                    'name' => 'test-name',
                ]
            ),
            $changedDbRow,
        );
    }
}
