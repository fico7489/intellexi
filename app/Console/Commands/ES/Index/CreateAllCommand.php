<?php

namespace App\Console\Commands\ES\Index;

use App\ESModule\Client\IndexClient;
use Illuminate\Console\Command;

class CreateAllCommand extends Command
{
    protected $signature = 'es:index:create-all';

    public function __construct(
        private readonly IndexClient $indexClient,
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->indexClient->createAll([
            'prefix_test',
            'prefix_test2',
        ]);
    }
}
