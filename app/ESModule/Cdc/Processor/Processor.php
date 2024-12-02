<?php

namespace App\ESModule\Cdc\Processor;

use App\ESModule\Cdc\Converter\ConverterInterface;
use App\ESModule\Cdc\Event\CdcChangedRows;
use App\ESModule\Cdc\Event\CdcPayloads;
use App\ESModule\Cdc\Grouper\Grouper;
use Psr\EventDispatcher\EventDispatcherInterface;

 class Processor
{
    public function __construct(
        private Grouper $grouper,
        private  EventDispatcherInterface $dispatcher,
        private  ConverterInterface $converter,
    ) {
    }

    public function process(array $payloads): void
    {
        $this->dispatcher->dispatch(new CdcPayloads($payloads));

        $changedRows = $this->converter->convert($payloads);

        $changedRowsGrouped = $this->grouper->group($changedRows);

        $this->dispatcher->dispatch(new CdcChangedRows($changedRowsGrouped));
    }
}
