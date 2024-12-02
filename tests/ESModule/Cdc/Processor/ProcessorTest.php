<?php

namespace Tests\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;
use App\ESModule\Cdc\Event\CdcChangedRowsGrouped;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Cdc\Event\CdcPayloadsEvent;
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

        $changedRow = new CdcDto('test', 'test', 'test', 'test', [], []);
        $changedRows = [$changedRow];

        $changedRowGrouped = new ChangedRowGroupedDto('test2', 'test2', 'test2', 'test2', [], []);
        $changedRowsGrouped = [$changedRowGrouped];

        $this->mock(Grouper::class, function ($mock) use ($changedRows, $changedRowsGrouped) {
            $mock->shouldReceive('group')
                ->withArgs(function ($changedRowsActual) use ($changedRows) {
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

        $this->mock(EventDispatcherInterface::class, function ($mock) use ($changedRowsGrouped, $changedRows, $payloads) {
            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcPayloadsEvent $event) use ($payloads) {
                    $this->assertEquals($payloads, $event->getCdcPayloads());

                    return true;
                })
                ->once();

            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcDtosEvent $event) use ($changedRows) {
                    $this->assertEquals($changedRows, $event->getCdcDtos());

                    return true;
                })
                ->once();

            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcChangedRowsGrouped $event) use ($changedRowsGrouped) {
                    $this->assertEquals($changedRowsGrouped, $event->getChangedRowsGrouped());

                    return true;
                })
                ->once();
        });

        app(Processor::class)->process($payloads);
    }
}
