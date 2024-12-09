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
            $changedFieldsMerged = $cdcDto->getChangedFields();

            $indexNamesForSync = $this->findIndexNamesForSync($cdcDto);
            if (0 === count($indexNamesForSync)) {
                continue;
            }

            // detect identifier
            $identifierValue = $this->databaseAdapter->detectIdentifierValue($tableName, $data);

            // we will group each cdcDto inside array with tableName->identifierValue, so that we remove duplicated
            if (CdcDto::TYPE_DELETE === $type) {
                if (isset($cdcSyncableDtos[$tableName][$identifierValue]) && CdcDto::TYPE_DELETE === $cdcSyncableDtos[$tableName][$identifierValue]->getType()) {
                    throw new Exception('Delete already added');
                }

                $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_DELETE, $cdcDto, $identifierValue, [], $indexNamesForSync);
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($cdcSyncableDtos[$tableName][$identifierValue])) {
                    throw new Exception('Insert after insert, delete or update');
                }

                $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_UPSERT, $cdcDto, $identifierValue, [], $indexNamesForSync);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                if (!isset($cdcSyncableDtos[$tableName][$identifierValue])) {
                    // item is not set
                    $changedFieldsMerged = $cdcDto->getChangedFields();
                    $cdcSyncableDtos[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_UPSERT, $cdcDto, $identifierValue, $changedFieldsMerged, $indexNamesForSync);
                } else {
                    // item is already set

                    /** @var CdcSyncableDto $cdcSyncableDto */
                    $cdcSyncableDto = $cdcSyncableDtos[$tableName][$identifierValue];

                    if (CdcSyncableDto::TYPE_DELETE === $cdcSyncableDto->getType()) {
                        throw new Exception('Update after delete');
                    }

                    // if we already have item stored as insert or update we will merge it with new item

                    // we will merge changed fields and use data of newer cdcDto
                    if (CdcSyncableDto::TYPE_UPSERT === $cdcSyncableDto->getType()) {
                        // merge indexNames
                        $indexNamesMerged = array_unique(array_merge(
                            $cdcSyncableDto->getIndexNames(),
                            $indexNamesForSync
                        ));
                        $cdcSyncableDto->setIndexNames($indexNamesMerged);

                        // merge changedFields
                        $changedFieldsMerged = array_unique(array_merge(
                            $cdcSyncableDto->getChangedFields(),
                            $changedFieldsMerged
                        ));
                        $cdcSyncableDto->setChangedFields($changedFieldsMerged);

                        // newer data
                        $cdcSyncableDto->setData($cdcDto->getData());

                        // set item
                        $cdcSyncableDtos[$tableName][$identifierValue] = $cdcSyncableDto;
                    }

                    // if previous type is insert we will use empty changedFields
                }
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

    private function findIndexNamesForSync(CdcDto $cdcDto): array
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

    private function createSyncDbRow(
        string $type,
        CdcDto $cdcDto,
        mixed $identifierValue,
        array $changedFields,
        array $indexNames,
    ): CdcSyncableDto {
        return new CdcSyncableDto(
            $cdcDto->getTableName(),
            $type,
            $cdcDto->getData(),
            $changedFields,
            $identifierValue,
            $indexNames,
        );
    }
}
