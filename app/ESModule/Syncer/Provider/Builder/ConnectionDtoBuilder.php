<?php

namespace App\ESModule\Syncer\Provider\Builder;

use App\ES\Connection\DefaultConnection;
use App\ESModule\Config\Dto\ConnectionDto;
use App\ESModule\Config\Dto\IndexDto;

class ConnectionDtoBuilder
{
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
                $indexDefiner->getMapping([]),
                $indexDefiner->getSettings([]),
                $connectionDto
            );
        }

        $connectionDto->setIndexes($indexesDtos);

        return $connectionDto;
    }
}
