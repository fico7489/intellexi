<?php

namespace App\ESModule\Cdc\Job;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ChangedDbRowJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $database,
        private readonly string $table,
        private readonly string $type,
        private readonly string $identifier,
        private readonly array $changedFields = [],
    ) {}

    public function handle(): void
    {
        dump('handle');

        //TODO
    }
}
