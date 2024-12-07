<?php

namespace App\ESModule\Syncer\Fetcher;

use App\ESModule\Config\Interface\IndexModelInterface;

class DataFetcher
{
    public function fetch(IndexModelInterface $index, object $model)
    {
        $data = [];

        // TODO decorate

        $data = $index->getData($data, $model);

        return $data;
    }
}
