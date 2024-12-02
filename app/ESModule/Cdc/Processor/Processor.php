<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Event\CdcGrouped;
use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Grouper\Grouper;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class Processor
{
    public function __construct(
        private Grouper $grouper,
        private EventDispatcherInterface $dispatcher,
        private readonly ConverterInterface $converter,
    ) {
    }

    public function process(array $payloads): void
    {
        $this->dispatcher->dispatch(new CdcRaw($payloads));

        $data = [];
        foreach ($payloads as $payload) {
            $data[] = $this->converter->convert($payload);
        }

        $dataGrouped = $this->grouper->group($data);

        $this->dispatcher->dispatch(new CdcGrouped($dataGrouped));
    }
}
