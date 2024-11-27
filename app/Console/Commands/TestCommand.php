<?php

namespace App\Console\Commands;

use App\ES\ApplicationIndex;
use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

        $modelData = $this->fetchData($data, $model);

        dd($modelData);
    }

    private function fetchData(array $data, Model $model): array
    {
        $modelData = [];
        foreach ($data as $key => $value) {
            if(is_int($key)){
                $modelData[$value] = $model->{$value};
            }else{
                $relation = $model->{$key};

                if($relation instanceof Model){
                    $modelData[$key] = $this->fetchData($value, $relation);
                }elseif ($relation instanceof Collection){
                    $modelData[$key] = [];
                    foreach ($relation as $item){
                        $modelData[$key][] = $this->fetchData($value, $item);
                    }
                }
            }
        }

        return $modelData;
    }
}
