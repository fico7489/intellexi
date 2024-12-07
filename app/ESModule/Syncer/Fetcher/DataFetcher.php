<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexDefinerModelInterface;

class DataFetcher
{
    public function fetch(IndexDefinerModelInterface $index, object $model)
    {
        $data = [];

        // TODO decorate

        $data = $index->getData($data, $model);

        return $data;
    }
}
