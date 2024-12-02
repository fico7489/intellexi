<?php

namespace Tests\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Dto\ChangedRowDto;
use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Processor\Processor;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\TestCase;

class ProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $payload = '{"test" : "payload"}';
        $payloads = [$payload];

        $changedRow = new ChangedRowDto('test', 'test', 'test', 'test', [], []);
        $changedRows = [$changedRow];

        $changedRowsGrouped = [$changedRow];

        $this->mock(Grouper::class, function ($mock) use ($changedRows, $changedRowsGrouped) {
            $mock->shouldReceive('group')
                ->withArgs(function ($changedRowsActual) use ($changedRows, $changedRowsGrouped) {
                    $this->assertEquals($changedRows, $changedRowsActual);

                    return true;
                })
                ->once()
                ->andReturn($changedRowsGrouped);
        });

        $this->mock(ConverterInterface::class, function ($mock) use ($payloads, $changedRows) {
            $mock->shouldReceive('convert')
                ->withArgs(function ($payloadsActual) use ($payloads) {
                    $this->assertEquals($payloads, $payloadsActual);

                    return true;
                })
                ->once()
                ->andReturn($changedRows)
            ;
        });

        $this->mock(EventDispatcherInterface::class, function ($mock) use ($payloads) {
            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcRaw $cdcRaw) use ($payloads) {
                    $this->assertEquals($payloads, $cdcRaw->getPayloads());

                    return true;
                })
                ->once();

            $mock->shouldReceive('dispatch')
                /*->withArgs(function (CdcGrouped $cdcRaw) ) {
                    $this->assertEquals($payloadGrouped, $cdcRaw->getPayload());

                    return true;
                })*/
                ->once();
        });

        app(Processor::class)->process($payloads);
    }
}
