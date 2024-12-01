<?php

namespace App\ESModule\Syncer;

use App\ESModule\Syncer\Dto\ChangedDbRow;

class Syncer
{
    public function sync(ChangedDbRow $changedDbRow)
    {
        dump(22, $changedDbRow);
    }
}
