<?php

namespace Tests\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentCreator;
use Tests\TestCase;

class DocumentCreatorTest extends TestCase
{
    public function testService()
    {
        $documentDtos = app(DocumentCreator::class)->create();
    }
}
