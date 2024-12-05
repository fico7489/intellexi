<?php

namespace Tests\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Cdc\Event\CdcPayloadsEvent;
use App\ESModule\Cdc\Processor\Processor;
use Mockery\MockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\TestCase;

class ProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $payload = '{"test" : "payload"}';
        $payloads = [$payload];

        $cdcDto = new CdcDto('test', 'test', 'test', [], [], []);
        $cdcDtos = [$cdcDto];

        $this->mock(ConverterInterface::class, function (MockInterface $mock) use ($payloads, $cdcDtos) {
            $mock->expects('convertCdcPayloadsToCdcDtos')
                ->withArgs(function ($payloadsActual) use ($payloads) {
                    $this->assertEquals($payloads, $payloadsActual);

                    return true;
                })
                ->once()
                ->andReturns($cdcDtos);
        });

        $this->mock(EventDispatcherInterface::class, function (MockInterface $mock) use ($cdcDtos, $payloads) {
            $mock->expects('dispatch')
                ->withArgs(function (CdcPayloadsEvent $event) use ($payloads) {
                    $this->assertEquals($payloads, $event->getCdcPayloads());

                    return true;
                })->once();

            $mock->expects('dispatch')
                ->withArgs(function (CdcDtosEvent $event) use ($cdcDtos) {
                    $this->assertEquals($cdcDtos, $event->getCdcDtos());

                    return true;
                })->once();
        });

        app(Processor::class)->processCdcPayloads($payloads);
    }
}
