<?php

namespace App\ESModule\Cdc\Grouper;

use App\ESModule\Cdc\Dto\ChangedDbRow;
use App\ESModule\Cdc\Dto\SyncDbRow;

class Grouper
{
    public function group($data2): array
    {
        $data = [];
        foreach ($data2 as $changedDbRow) {
            $table = $changedDbRow->getTable();
            $identifier = $changedDbRow->getIdentifier();
            $changedFields = $changedDbRow->getChangedFields();

            $type = $changedDbRow->getType();
            if (ChangedDbRow::TYPE_DELETE === $type) {
                $data[$table][$identifier] = $this->createSyncDbRow($changedDbRow, ChangedDbRow::TYPE_DELETE);
            } elseif (ChangedDbRow::TYPE_UPDATE === $type) {
                if (!isset($data[$table][$identifier])){
                    $data[$table][$identifier] = $this->createSyncDbRow($changedDbRow, SyncDbRow::TYPE_UPSERT);
                }else{
                    /** @var SyncDbRow $syncDbRowExisting */
                    $syncDbRowExisting = $data[$table][$identifier];

                    if($syncDbRowExisting->getType() === ChangedDbRow::TYPE_DELETE){
                        //do nothing
                    }

                    if($syncDbRowExisting->getType() === SyncDbRow::TYPE_UPSERT){
                        $changedFields = array_unique(array_merge(
                            $syncDbRowExisting->getChangedFields(),
                            $changedFields
                        ));

                        $syncDbRowExisting->setChangedFields($changedFields);
                        $syncDbRowExisting->setData($changedDbRow->getData());

                        $data[$table][$identifier] = $syncDbRowExisting;
                    }
                }
            } elseif (ChangedDbRow::TYPE_INSERT === $type) {
                $data[$table][$identifier] = $changedDbRow;
            }
        }

        return $data;
    }

    private function createSyncDbRow(ChangedDbRow $changedDbRow, string $type): SyncDbRow
    {
        return new SyncDbRow(
            $changedDbRow->getDatabase(),
            $changedDbRow->getTable(),
            $type,
            $changedDbRow->getIdentifier(),
            $changedDbRow->getChangedFields(),
            $changedDbRow->getData()
        );
    }
}
