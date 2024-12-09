<?php

namespace App\ESModule\Syncer\Creator\CdcSyncable;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use App\ESModule\Syncer\Creator\CdcSyncable\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Provider\ConfigProvider;

class CdcSyncableCreator
{
    public function __construct(
        private readonly DatabaseAdapter $databaseAdapter,
        private readonly ConfigProvider $configProvider,
        private readonly ShouldSyncDetector $shouldSyncDetector,
    ) {
    }

    /**
     * @param array<CdcDto> $cdcDtos //TODO try to remove CdcDto, so that package will be independent
     *
     * @return array<CdcSyncableDto>
     *
     * @throws Exception
     */
    public function create(array $cdcDtos): array
    {
        $cdcSyncableDtos = [];
        foreach ($cdcDtos as $cdcDto) {
            $databaseName = $cdcDto->getDatabaseName();
            $tableName = $cdcDto->getTableName();
            $type = $cdcDto->getType();
            $data = $cdcDto->getData();
            $changedFields = $cdcDto->getChangedFields();

            $indexNamesForSync = $this->findIndexNamesForSync($cdcDto);
            if (0 === count($indexNamesForSync)) {
                continue;
            }

            // detect identifier
            $identifierValue = $this->databaseAdapter->detectIdentifierValue($tableName, $data);

            // we will group each cdcDto inside array with tableName->identifierValue, so that we remove duplicated
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($cdcSyncableDtos[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $cdcSyncableDtos[$tableName][$identifierValue]->getType()) {
                    // it is not possible to receive two cdc for deleting a row
                    throw new Exception('Grouper: delete already deleted');
                }

                $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, CdcSyncableDto::TYPE_DELETE, $identifierValue, $indexNamesForSync);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($cdcSyncableDtos[$tableName][$identifierValue])) {
                    // item is not set
                    $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, CdcSyncableDto::TYPE_UPSERT, $identifierValue, $indexNamesForSync);
                } else {
                    // item is already set

                    /** @var CdcSyncableDto $cdcSyncableDto */
                    $cdcSyncableDto = $cdcSyncableDtos[$tableName][$identifierValue];

                    if (CdcSyncableDto::TYPE_DELETE === $cdcSyncableDto->getType()) {
                        // it is not possible to receive update after row is already deleted
                        throw new Exception('Grouper: update detected after delete');
                    }

                    // if we already have item stored as insert or update we will merge it with new item
                    // we will merge changed fields and use data of newer cdcDto
                    if (CdcSyncableDto::TYPE_UPSERT === $cdcSyncableDto->getType()) {
                        $changedFields = array_unique(array_merge(
                            $cdcSyncableDto->getChangedFields(),
                            $changedFields
                        ));

                        $cdcSyncableDto->setChangedFields($changedFields);
                        $cdcSyncableDto->setData($cdcDto->getData());

                        $cdcSyncableDtos[$tableName][$identifierValue] = $cdcSyncableDto;
                    }
                }
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($cdcSyncableDtos[$tableName][$identifierValue])) {
                    // row could not be inserted more times
                    throw new Exception('Grouper: insert detected after insert, delete or update');
                }

                $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow($cdcDto, CdcSyncableDto::TYPE_UPSERT, $identifierValue, $indexNamesForSync);
            }
        }

        // we will now put all DTOs to array
        $cdcSyncableDtosFlatten = [];
        foreach ($cdcSyncableDtos as $tableName => $tableData) {
            foreach ($tableData as $identifierValue => $cdcSyncableDto) {
                $cdcSyncableDtosFlatten[] = $cdcSyncableDto;
            }
        }

        return $cdcSyncableDtosFlatten;
    }

    private function findIndexNamesForSync(CdcDto $cdcDto)
    {
        // don't do sync if database is not supported
        $databaseName = $cdcDto->getDatabaseName();
        if (!$this->configProvider->isDatabaseNameForSync($databaseName)) {
            return [];
        }

        $tableName = $cdcDto->getTableName();
        $syncMap = $this->configProvider->getConfigDto()->getSyncMap();
        $syncMapForTableName = $syncMap[$tableName] ?? [];

        // don't do sync if tableName is not is for sync
        if (0 === count($syncMapForTableName)) {
            return [];
        }

        $type = $cdcDto->getType();
        $changedFields = $cdcDto->getChangedFields();

        $indexNamesForSync = [];
        foreach ($syncMapForTableName as $indexName => $changedFieldsTriggers) {
            if ($this->shouldSyncDetector->detect($type, $changedFields, $changedFieldsTriggers)) {
                $indexNamesForSync[] = $indexName;
            }
        }

        return $indexNamesForSync;
    }

    private function createSyncDbRow(CdcDto $cdcDto, string $type, mixed $identifierValue, $indexNames): CdcSyncableDto
    {
        return new CdcSyncableDto(
            $cdcDto->getTableName(),
            $type,
            $cdcDto->getData(),
            $cdcDto->getChangedFields(),
            $identifierValue,
            $indexNames,
        );
    }
}
