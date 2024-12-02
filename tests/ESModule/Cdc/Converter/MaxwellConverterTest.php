<?php

namespace Tests\ESModule\Cdc\Converter;

use App\ESModule\Cdc\Converter\MaxwellConverter;
use App\ESModule\Cdc\Dto\CdcDto;
use Tests\TestCase;

class MaxwellConverterTest extends TestCase
{
    public function testInsert()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"insert","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}}';
        $payloads = [$payload];

        $changedDbRows = app(MaxwellConverter::class)->convert($payloads);

        $this->assertEquals(
            new CdcDto(
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
            $changedDbRows[0],
        );
    }

    public function testDelete()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"delete","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}}';
        $payloads = [$payload];

        $changedDbRows = app(MaxwellConverter::class)->convert($payloads);

        $this->assertEquals(
            new CdcDto(
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
            $changedDbRows[0],
        );
    }

    public function testIUpdate()
    {
        $payload = '{"database":"test-db","table":"test-table","type":"update","ts":1234,"xid":5678,"commit":true,"data":{"id":1,"name":"test-name"}, "old" : {"name" : "test-name-old"}}';
        $payloads = [$payload];

        $changedDbRows = app(MaxwellConverter::class)->convert($payloads);

        $this->assertEquals(
            new CdcDto(
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
            $changedDbRows[0],
        );
    }
}
