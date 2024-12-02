<?php

namespace App\ESModule\Syncer\SyncItemsFetcher;

use App\ESModule\Cdc\Dto\ChangedRowGroupedDto;

class ItemsFetcher
{
    public function __construct(
        private readonly ItemsRootModelFetcher $itemsRootModelFetcher,
        private readonly ItemsRelatedModelsFetcher $itemsRelatedModelsFetcher,
    ) {
    }

    public function fetch(ChangedRowGroupedDto $changedRowGrouped): array
    {
        $items = [];

        $items = $this->itemsRootModelFetcher->fetch($items, $changedRowGrouped);

        $items = $this->itemsRelatedModelsFetcher->fetch($items, $changedRowGrouped);

        // TODO group

        return $items;
    }
}
