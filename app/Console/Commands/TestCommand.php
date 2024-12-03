<?php

namespace App\Console\Commands;

use App\ESModule\Syncer\Eloquent\ModelMapper;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function handle(): void
    {
        $milliseconds = floor(microtime(true) * 1000);

        $data = app(ModelMapper::class)->fetchAllClassNames();
        dump($data);

        dump((floor(microtime(true) * 1000) - $milliseconds).' ms');
    }
}
