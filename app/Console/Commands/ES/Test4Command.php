<?php

namespace App\Console\Commands\ES;

use App\Jobs\ProcessPodcast;
use App\Models\Role;
use Illuminate\Console\Command;

class Test4Command extends Command
{
    protected $signature = 'test4';

    public function handle()
    {
        $role = Role::create(['name' => random_int(1, 100000)]);

        /*$role->update(['name' => random_int(1, 100000)]);

        $role->delete();*/

        //ProcessPodcast::dispatch(12)->onQueue('high');
    }
}
