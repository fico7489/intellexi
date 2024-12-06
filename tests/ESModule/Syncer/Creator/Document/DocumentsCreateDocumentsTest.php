<?php

namespace Tests\ESModule\Syncer\Creator\Document;

use App\ESModule\Syncer\Creator\Document\DocumentsCreator;
use App\ESModule\Syncer\Creator\Document\DocumentsCreator\DocumentCreator;
use App\ESModule\Syncer\Creator\Document\Dto\DocumentDto;
use App\ESModule\Syncer\Creator\Document\Helper\ShouldSyncDetector;
use App\ESModule\Syncer\Creator\SyncItem\Dto\SyncItemDto;
use App\ESModule\Syncer\Mapper\IndexMapper\IndexMapper;
use App\ESModule\Syncer\Mapper\SyncMapper\SyncMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\SyncItem\TestCase;

class DocumentsCreateDocumentsTest extends TestCase
{
    public function testNotMatchedTable()
    {
        $syncItemDto = new SyncItemDto('test_table', SyncItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table2' => [
                    'test-index' => [
                        'id',
                    ],
                ],
            ];

            $mock->allows('create')->andReturn($syncMapping)->once();
        });

        $this->mock(ShouldSyncDetector::class, function (MockInterface $mock) {
            $mock->allows('detect')->never();
        });

        $this->mock(IndexMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchIndexByIndexName')->never();
        });

        $documents = app(DocumentsCreator::class)->create([$syncItemDto]);

        $this->assertEquals(0, count($documents));
    }

    public function testNotMatchedShouldSync()
    {
        $syncItemDto = new SyncItemDto('test_table', SyncItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table' => [
                    'test-index' => [
                        'id',
                    ],
                ],
            ];

            $mock->allows('create')->andReturn($syncMapping)->once();
        });

        $this->mock(ShouldSyncDetector::class, function (MockInterface $mock) {
            $mock->allows('detect')->andReturn(false);
        });

        $this->mock(IndexMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchIndexByIndexName')->never();
        });

        $documents = app(DocumentsCreator::class)->create([$syncItemDto]);

        $this->assertEquals(0, count($documents));
    }

    public function testAddOneItem()
    {
        $syncItemDto = new SyncItemDto('test_table', SyncItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);
        $documentDto = new DocumentDto('test', 1, ['id' => 1], DocumentDto::TYPE_UPSERT);

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table' => [
                    'test-index' => [
                        'id',
                    ],
                ],
            ];

            $mock->allows('create')->andReturn($syncMapping)->once();
        });

        $this->mock(ShouldSyncDetector::class, function (MockInterface $mock) {
            $mock->allows('detect')->andReturn(true);
        });

        $this->mock(IndexMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchIndexByIndexName')->andReturn()->once();
        });

        $this->mock(DocumentCreator::class, function (MockInterface $mock) use ($documentDto) {
            $mock->allows('create')->andReturn([$documentDto]);
        });

        $documents = app(DocumentsCreator::class)->create([$syncItemDto]);

        $this->assertEquals(1, count($documents));
    }

    public function testAddTwoItems()
    {
        $syncItemDto = new SyncItemDto('test_table', SyncItemDto::TYPE_UPSERT, ['id' => 1], ['id'], []);
        $syncItemDto2 = new SyncItemDto('test_table2', SyncItemDto::TYPE_UPSERT, ['id' => 2], ['id'], []);
        $documentDto = new DocumentDto('test', 1, ['id' => 1], DocumentDto::TYPE_UPSERT);
        $documentDto2 = new DocumentDto('test2', 2, ['id' => 2], DocumentDto::TYPE_UPSERT);

        $this->mock(SyncMapper::class, function (MockInterface $mock) {
            $syncMapping = [
                'test_table' => [
                    'test-index' => [
                        'id',
                    ],
                ],
                'test_table2' => [
                    'test-index2' => [
                        'id',
                    ],
                ],
            ];

            $mock->allows('create')->andReturn($syncMapping)->once();
        });

        $this->mock(ShouldSyncDetector::class, function (MockInterface $mock) {
            $mock->allows('detect')->andReturn(true);
        });

        $this->mock(IndexMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchIndexByIndexName')->andReturn()->times(2);
        });

        $this->mock(DocumentCreator::class, function (MockInterface $mock) use ($documentDto, $documentDto2) {
            $mock->allows('create')->andReturn([$documentDto])->once();
            $mock->allows('create')->andReturn([$documentDto2])->once();
        });

        $documents = app(DocumentsCreator::class)->create([$syncItemDto, $syncItemDto2]);

        $this->assertEquals(2, count($documents));
    }
}
