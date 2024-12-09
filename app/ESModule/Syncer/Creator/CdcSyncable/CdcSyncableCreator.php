<?php

namespace App\ESModule\Syncer\Creator\CdcSyncable;

use App\ESModule\Cdc\Dto\CdcDto;
use App\ESModule\Syncer\Adapter\DatabaseAdapter\DatabaseAdapter;
use App\ESModule\Syncer\Creator\CdcSyncable\Dto\CdcSyncableDto;
use App\ESModule\Syncer\Creator\CdcSyncable\Exception\Exception;
use App\ESModule\Syncer\Creator\CdcSyncable\Helper\IndexNamesForSyncFinder;

class CdcSyncableCreator
{
    public function __construct(
        private readonly DatabaseAdapter $databaseAdapter,
        private readonly IndexNamesForSyncFinder $indexNamesForSyncFinder,
    ) {
    }

    /**
     * @param array<CdcDto> $cdcDtos //TODO try to remove CdcDto, so that package will be independent, PSR
     *
     * @return array<CdcSyncableDto>
     *
     * @throws Exception
     */
    public function create(array $cdcDtos): array
    {
        $cdcSyncableDtosGrouped = [];

        foreach ($cdcDtos as $cdcDto) {
            // detect by $cdcDto all indexNames that should be synced, if we can't find any skip that $cdcDto
            $indexNamesForSync = $this->indexNamesForSyncFinder->findIndexNamesForSync($cdcDto);
            if (0 === count($indexNamesForSync)) {
                continue;
            }

            // detect identifier
            $tableName = $cdcDto->getTableName();
            $data = $cdcDto->getData();
            $identifierValue = $this->databaseAdapter->detectIdentifierValue($tableName, $data);

            // we will group each cdcDto inside array with [tableName][identifierValue], so that we remove duplicated
            $type = $cdcDto->getType();
            if (CdcDto::TYPE_DELETE === $type) {
                if (
                    isset($cdcSyncableDtosGrouped[$tableName][$identifierValue])
                    && CdcDto::TYPE_DELETE === $cdcSyncableDtosGrouped[$tableName][$identifierValue]->getType()
                ) {
                    throw new Exception('Delete already added');
                }

                $cdcSyncableDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_DELETE, $cdcDto, $identifierValue, [], $indexNamesForSync);
            } elseif (CdcDto::TYPE_INSERT === $type) {
                if (isset($cdcSyncableDtosGrouped[$tableName][$identifierValue])) {
                    throw new Exception('Insert after insert, delete or update');
                }

                $cdcSyncableDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_UPSERT, $cdcDto, $identifierValue, [], $indexNamesForSync);
            } elseif (CdcDto::TYPE_UPDATE === $type) {
                $changedFields = $cdcDto->getChangedFields();

                if (!isset($cdcSyncableDtosGrouped[$tableName][$identifierValue])) {
                    // item is not set
                    $cdcSyncableDtosGrouped[$tableName][$identifierValue] = $this->createSyncDbRow(CdcSyncableDto::TYPE_UPSERT, $cdcDto, $identifierValue, $changedFields, $indexNamesForSync);
                } else {
                    // item is already set

                    /** @var CdcSyncableDto $cdcSyncableDto */
                    $cdcSyncableDto = $cdcSyncableDtosGrouped[$tableName][$identifierValue];

                    if (CdcSyncableDto::TYPE_DELETE === $cdcSyncableDto->getType()) {
                        throw new Exception('Update after delete');
                    }

                    // if we already have item stored as insert or update we will merge it with a new item

                    // merge and set indexNames
                    $indexNamesMerged = array_unique(array_merge(
                        $cdcSyncableDto->getIndexNamesForSync(),
                        $indexNamesForSync
                    ));
                    $cdcSyncableDto->setIndexNames($indexNamesMerged);

                    // merge and set changedFields
                    if (count($cdcSyncableDto->getChangedFields()) > 0) {
                        // when count === 0 then previous item was created and we will leave empty list
                        $changedFieldsMerged = array_unique(array_merge(
                            $cdcSyncableDto->getChangedFields(),
                            $changedFields
                        ));
                        $cdcSyncableDto->setChangedFields($changedFieldsMerged);
                    }

                    // set newer data
                    $cdcSyncableDto->setData($cdcDto->getData());

                    // set item
                    $cdcSyncableDtosGrouped[$tableName][$identifierValue] = $cdcSyncableDto;
                }
            }
        }

        // we will now flatten all DTOs to simple array
        $cdcSyncableDtosFlatten = [];
        foreach ($cdcSyncableDtosGrouped as $tableName => $tableData) {
            foreach ($tableData as $identifierValue => $cdcSyncableDto) {
                $cdcSyncableDtosFlatten[] = $cdcSyncableDto;
            }
        }

        return $cdcSyncableDtosFlatten;
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
