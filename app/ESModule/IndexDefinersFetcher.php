<?php

namespace App\ESModule;

use App\ES\ApplicationIndex;
use App\ES\UserIndex;

class IndexDefinersFetcher
{
    public function fetchAll(): array
    {
        return [
            app(ApplicationIndex::class),
            app(UserIndex::class),
        ];
    }
}
