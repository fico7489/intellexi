<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Interface\IndexModelInterface;
use App\ESModule\Syncer\Provider\Builder\Dto\ConnectionDto;
use App\ESModule\Syncer\Provider\Builder\Dto\IndexDto;

class ConnectionDtoBuilder
{
    /**
     * @param array<IndexModelInterface> $indexDefiners
     */
    public function build(DefaultConnection $connectionDefiner, array $indexDefiners): ConnectionDto
    {
        $connectionDto = new ConnectionDto(
            $connectionDefiner->getName(),
            $connectionDefiner->getHost(),
            $connectionDefiner->getPort(),
            $connectionDefiner->getPrefix(),
        );

        $indexesDtos = [];
        foreach ($indexDefiners as $indexDefiner) {
            $indexesDtos[$indexDefiner->getIndexName()] = new IndexDto(
                $indexDefiner->getIndexName(),
                $indexDefiner->getClassNameOrm(),
                $indexDefiner->getMapping([]),
                $indexDefiner->getSettings([]),
                $connectionDto
            );
        }

        $connectionDto->setIndexes($indexesDtos);

        return $connectionDto;
    }
}
