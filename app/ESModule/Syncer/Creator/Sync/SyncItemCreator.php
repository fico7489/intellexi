<?php

namespace App\ESModule\Syncer\Creator\Sync;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Creator\Sync\Dto\SyncDto;
use App\ESModule\Syncer\Creator\Sync\Exception\GrouperException;
use App\ESModule\Syncer\Mapper\DatabaseMapper\DatabaseMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;

class SyncItemCreator
{
    public function __construct(
        private readonly DatabaseMapper $databaseMapper,
        private readonly SyncMapper $syncMapper,
    ) {
    }

    /**
     * It converts CdcDtos to SyncDtos.
     *
     * @param array<CdcDto> $cdcDtos
     *
     * @return array<SyncDto>
     *
     * @throws GrouperException
     */
    public function create(array $cdcDtos): array
    {
        $syncDtosGrouped = [];
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
            $identifierValue = $this->databaseMapper->detectIdentifierValue($tableName, $data);

            // we will group each cdcDto inside array with table->identifier keys, so that we remove duplicated and detect any problems
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($syncDtosGrouped[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $syncDtosGrouped[$tableName][$identifierValue]->getType()) {
                    // it is not possible to receive two cdc for deleting a row
                    throw new GrouperException('Grouper: delete already deleted');
                }

                $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_DELETE, $identifierValue);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($syncDtosGrouped[$tableName][$identifierValue])) {
                    // item is not set
                    $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
                } else {
                    // item is already set

                    /** @var SyncDto $syncDto */
                    $syncDto = $syncDtosGrouped[$tableName][$identifierValue];

                    if (SyncDto::TYPE_DELETE === $syncDto->getType()) {
                        // it is not possible to receive update after row is already deleted
                        throw new GrouperException('Grouper: update detected after delete');
                    }

                    // if we already have item stored as insert or update we will merge it with new item
                    // we will merge changed fields and use data of newer cdcDto
                    if (SyncDto::TYPE_UPSERT === $syncDto->getType()) {
                        $changedFields = array_unique(array_merge(
                            $syncDto->getChangedFields(),
                            $changedFields
                        ));

                        $syncDto->setChangedFields($changedFields);
                        $syncDto->setData($cdcDto->getData());

                        $syncDtosGrouped[$tableName][$identifierValue] = $syncDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($syncDtosGrouped[$tableName][$identifierValue])) {
                    // row could not be inserted more times
                    throw new GrouperException('Grouper: insert detected after insert, delete or update');
                }

                $syncDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, SyncDto::TYPE_UPSERT, $identifierValue);
            }
        }

        // we will now put all DTOs to array
        $syncDtos = [];
        foreach ($syncDtosGrouped as $tableName => $data) {
            foreach ($data as $identifierValue => $syncDto) {
                $syncDtos[] = $syncDto;
            }
        }

        return $syncDtos;
    }

    private function createSyncDbRow(CdcDto $cdcDto, string $type, mixed $identifierValue): SyncDto
    {
        $changedFields = $cdcDto->getChangedFields();

        if (CdcDto::TYPE_DELETE === $type) {
            $changedFields = [];
        }

        return new SyncDto(
            $cdcDto->getTableName(),
            $type,
            $cdcDto->getData(),
            $changedFields,
            $identifierValue,
        );
    }
}
