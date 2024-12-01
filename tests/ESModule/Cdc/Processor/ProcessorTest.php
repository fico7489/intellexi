<?php

namespace Tests\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Event\CdcGrouped;
use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Processor\Processor;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\TestCase;

class ProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $payload = ['{"test" : "payload"}'];
        $payloadGrouped = ['{"test" : "payload"}'];

        $this->mock(Grouper::class, function ($mock) use ($payloadGrouped, $payload) {
            $mock->shouldReceive('group')
                ->withArgs([$payload])
                ->once()
                ->andReturn($payloadGrouped);
        });

        $this->mock(EventDispatcherInterface::class, function ($mock) use ($payloadGrouped, $payload) {
            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcRaw $cdcRaw) use ($payload) {
                    $this->assertEquals($payload, $cdcRaw->getPayload());

                    return true;
                })
                ->once();

            $mock->shouldReceive('dispatch')
                ->withArgs(function (CdcGrouped $cdcRaw) use ($payloadGrouped) {
                    $this->assertEquals($payloadGrouped, $cdcRaw->getPayload());

                    return true;
                })
                ->once();
        });

        app(Processor::class)->process($payload);
    }
}
