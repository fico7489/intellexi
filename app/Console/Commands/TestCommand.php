<?php

namespace App\Console\Commands;

use App\ES\ApplicationIndex;
use App\Models\Application;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    public function __construct(private string $test)
    {
        parent::__construct();
    }

    public function handle()
    {
        $model = Application::find(1);

        $data = app(ApplicationIndex::class)->getData();

        $modelData = [];
        foreach ($data as $key => $value) {
            if(is_int($key)){
                $modelData[$value] = $model->{$value};
            }else{

            }
        }

        dd($modelData);
    }
}
