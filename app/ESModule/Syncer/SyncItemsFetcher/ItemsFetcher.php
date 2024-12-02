<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Syncer\CdcConverter\Dto\SyncRowDto;

class ItemsFetcher
{
    public function __construct(
        private readonly ItemsRootModelFetcher $itemsRootModelFetcher,
        private readonly ItemsRelatedModelsFetcher $itemsRelatedModelsFetcher,
    ) {
    }

    public function fetch(SyncRowDto $syncRowDto): array
    {
        $items = [];

        $items = $this->itemsRootModelFetcher->fetch($items, $syncRowDto);

        $items = $this->itemsRelatedModelsFetcher->fetch($items, $syncRowDto);

        // TODO group

        return $items;
    }
}
