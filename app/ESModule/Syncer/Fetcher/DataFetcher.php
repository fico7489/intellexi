<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;
use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;
use Illuminate\Database\Eloquent\Model;

class DataFetcher
{
    public function fetch(
        IndexDefinerModelInterface $index,
        Model                      $model,
        SyncRowDto                 $syncRowDto,
    ) {
        $data = [];

        // TODO decorate

        $data = $index->getData($data, $model);

        return $data;
    }
}
