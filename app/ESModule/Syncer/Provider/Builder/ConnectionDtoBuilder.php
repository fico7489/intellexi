<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Provider\Builder\Dto\ConnectionDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class ConnectionDtoBuilder
{
    public function __construct(
        private readonly SyncMapBuilder $syncMapBuilder,
    ) {
    }

    /**
     * @param array<IndexModelInterface> $indexDefiners
     */
    public function build(DefaultConnection $connectionDefiner, array $indexDefiners): ConnectionDto
    {
        $syncMap = $this->syncMapBuilder->buildSyncMapping($indexDefiners);

        $connectionDto = new ConnectionDto(
            $connectionDefiner->getName(),
            $connectionDefiner->getHost(),
            $connectionDefiner->getPort(),
            $connectionDefiner->getPrefix(),
            $syncMap,
        );

        $indexesDtos = [];
        foreach ($indexDefiners as $indexDefiner) {
            $indexesDtos[$indexDefiner->getIndexName()] = new IndexDto(
                $indexDefiner->getClassName(),
                $indexDefiner->getIndexName(),
                $indexDefiner->getMapping([]),
                $indexDefiner->getSettings([]),
                $connectionDto
            );
        }

        $connectionDto->setIndexes($indexesDtos);

        return $connectionDto;
    }
}
