<?php

namespace App\Console\Commands;

use App\ESModule\Syncer\ModelMapper;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        $data = app(ModelMapper::class)->fetchAllModelClassNames();
        dump($data);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
