<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Event\CdcDtosEvent;
use App\ESModule\Cdc\Event\CdcPayloadsEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class Processor
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private ConverterInterface $converter,
    ) {
    }

    public function processCdcPayloads(array $cdcPayloads): void
    {
        $this->dispatcher->dispatch(new CdcPayloadsEvent($cdcPayloads));

        $cdcDtos = $this->converter->convertCdcPayloadsToCdcDtos($cdcPayloads);
        $this->dispatcher->dispatch(new CdcDtosEvent($cdcDtos));
    }
}
