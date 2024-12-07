<?php

namespace App\ESModule\Syncer\Creator\SyncItem;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Creator\SyncItem\Exception\SyncItemCreatorException;
use App\ESModule\Syncer\Provider\ConfigProvider;

class SyncItemCreator
{
    public function __construct(
        private readonly DatabaseAdapter $databaseAdapter,
        private readonly ConfigProvider $syncMapper,
    ) {
    }

    /**
     * It converts CdcDtos to SyncItemDtos.
     *
     * @param array<CdcDto> $cdcDtos
     *
     * @return array<SyncItemDto>
     *
     * @throws SyncItemCreatorException
     */
    public function create(array $cdcDtos): array
    {
        $syncItemDtosGrouped = [];
        foreach ($cdcDtos as $cdcDto) {
            $databaseName = $cdcDto->getDatabaseName();
            $tableName = $cdcDto->getTableName();
            $type = $cdcDto->getType();
            $data = $cdcDto->getData();
            $changedFields = $cdcDto->getChangedFields();

            // don't do sync if database is not supported
            if (!$this->syncMapper->isDatabaseNameForSync($databaseName)) {
                continue;
            }

            // don't do sync if tableName is not is for sync
            if (!$this->syncMapper->isTableNameForSync($tableName)) {
                continue;
            }

            // detect identifier
            $identifierValue = $this->databaseAdapter->detectIdentifierValue($tableName, $data);

            // we will group each cdcDto inside array with table->identifier keys, so that we remove duplicated and detect any problems
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($syncItemDtosGrouped[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $syncItemDtosGrouped[$tableName][$identifierValue]->getType()) {
                    // it is not possible to receive two cdc for deleting a row
                    throw new SyncItemCreatorException('Grouper: delete already deleted');
                }

                $syncItemDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncItemDto::TYPE_DELETE, $identifierValue);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncItemDtosGrouped[$tableName][$identifierValue])) {
                    // item is not set
                    $syncItemDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncItemDto::TYPE_UPSERT, $identifierValue);
                } else {
                    // item is already set

                    /** @var SyncItemDto $syncItemDto */
                    $syncItemDto = $syncItemDtosGrouped[$tableName][$identifierValue];

                    if (SyncItemDto::TYPE_DELETE === $syncItemDto->getType()) {
                        // it is not possible to receive update after row is already deleted
                        throw new SyncItemCreatorException('Grouper: update detected after delete');
                    }

                    // if we already have item stored as insert or update we will merge it with new item
                    // we will merge changed fields and use data of newer cdcDto
                    if (SyncItemDto::TYPE_UPSERT === $syncItemDto->getType()) {
                        $changedFields = array_unique(array_merge(
                            $syncItemDto->getChangedFields(),
                            $changedFields
                        ));

                        $syncItemDto->setChangedFields($changedFields);
                        $syncItemDto->setData($cdcDto->getData());

                        $syncItemDtosGrouped[$tableName][$identifierValue] = $syncItemDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncItemDtosGrouped[$tableName][$identifierValue])) {
                    // row could not be inserted more times
                    throw new SyncItemCreatorException('Grouper: insert detected after insert, delete or update');
                }

                $syncItemDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncItemDto::TYPE_UPSERT, $identifierValue);
            }
        }

        // we will now put all DTOs to array
        $syncItemDtos = [];
        foreach ($syncItemDtosGrouped as $tableName => $data) {
            foreach ($data as $identifierValue => $syncItemDto) {
                $syncItemDtos[] = $syncItemDto;
            }
        }

        return $syncItemDtos;
    }

    private function createSyncDbRow(CdcDto $cdcDto, string $type, mixed $identifierValue): SyncItemDto
    {
        $changedFields = $cdcDto->getChangedFields();

        if (CdcDto::TYPE_DELETE === $type) {
            $changedFields = [];
        }

        return new SyncItemDto(
            $cdcDto->getTableName(),
            $type,
            $cdcDto->getData(),
            $changedFields,
            $identifierValue,
        );
    }
}
