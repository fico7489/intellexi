<?php

namespace Tests\ESModule\Syncer\Creator\SyncItem\Helper;

use App\ESModule\Syncer\Creator\SyncDocument\Helper\ShouldSyncDetector;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class ShouldSyncDetectorTest extends TestCase
{
    public function testIsForSync()
    {
        $this->assertEquals(true, app(ShouldSyncDetector::class)->detect(['id'], ['id', 'name']));
    }

    public function testIsForSync2()
    {
        $this->assertEquals(true, app(ShouldSyncDetector::class)->detect(['id', 'name'], ['id', 'name']));
    }

    public function testIsForSync3()
    {
        $this->assertEquals(true, app(ShouldSyncDetector::class)->detect(['id', 'name', 'test'], ['id', 'name']));
    }

    public function testIsNotForSync()
    {
        $this->assertEquals(false, app(ShouldSyncDetector::class)->detect([], ['id', 'name']));
    }

    public function testIsNotForSync2()
    {
        $this->assertEquals(false, app(ShouldSyncDetector::class)->detect(['id2'], ['id', 'name']));
    }

    public function testIsNotForSync3()
    {
        $this->assertEquals(false, app(ShouldSyncDetector::class)->detect(['id2', 'name2'], ['id', 'name']));
    }

    public function testIsNotForSync4()
    {
        $this->assertEquals(false, app(ShouldSyncDetector::class)->detect(['id2', 'name2', 'test2'], ['id', 'name']));
    }
}
