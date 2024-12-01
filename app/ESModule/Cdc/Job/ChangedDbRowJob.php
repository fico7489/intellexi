<?php

namespace App\ESModule\Cdc\Job;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChangedDbRowJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ChangedDbRow $changedDbRow,
    ) {
    }

    public function handle(): void
    {
        dump(1234, $this->changedDbRow);

        // TODO
    }
}
