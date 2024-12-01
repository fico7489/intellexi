<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Event\CdcRaw;
use App\ESModule\Cdc\Grouper\Grouper;
use App\ESModule\Cdc\Syncer\Handler;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class Processor
{
    public function __construct(
        private Grouper $grouper,
        private Handler $syncer,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(array $payload): void
    {
        $this->dispatcher->dispatch(new CdcRaw($payload));

        $this->syncer->handleRaw($payload);

        $dataGrouped = $this->grouper->group($payload);

        $this->syncer->handleGrouped($dataGrouped);
    }
}
