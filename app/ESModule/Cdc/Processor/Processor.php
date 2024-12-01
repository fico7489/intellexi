<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Event\CdcGrouped;
use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Grouper\Grouper;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class Processor
{
    public function __construct(
        private Grouper $grouper,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(array $payload): void
    {
        $this->dispatcher->dispatch(new CdcRaw($payload));

        $dataGrouped = $this->grouper->group($payload);

        //$this->dispatcher->dispatch(new CdcGrouped($dataGrouped));
    }
}
