<?php

namespace Tests\ESModule\Syncer\Creator\Sync\Helper;

use App\ESModule\Syncer\Creator\Document\Exception\DocumentCreatorException;
use App\ESModule\Syncer\Creator\Document\Helper\ModelsRelatedValidatorAndGrouper;
use App\ESModule\Syncer\Mapper\ModelMapper\ModelMapper;
use Mockery\MockInterface;
use Tests\ESModule\Syncer\Creator\Sync\TestCase;

class ModelsRelatedValidatorAndGrouperTest extends TestCase
{
    public function testBasic()
    {
        $object = new class {
            public $id = 1;
        };

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNameFromModel')->andReturn('test-table');
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(1)->once();
        });

        $relatedModels = app(ModelsRelatedValidatorAndGrouper::class)->validateAndGroup([$object], $object::class);

        $this->assertEquals(1, count($relatedModels));
        $this->assertEquals($object, $relatedModels[0]);
    }

    public function testTwoDifferent()
    {
        $object = new class {
            public $id = 1;
        };

        $object2 = clone $object;
        $object2->id = 2;

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNameFromModel')->andReturn('test-table');
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(1)->once();
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(2)->once();
        });

        $relatedModels = app(ModelsRelatedValidatorAndGrouper::class)->validateAndGroup([$object, $object2], $object::class);

        $this->assertEquals(2, count($relatedModels));
        $this->assertEquals($object, $relatedModels[0]);
        $this->assertEquals($object2, $relatedModels[1]);
    }

    public function testTwoSame()
    {
        $object = new class {
            public $id = 1;
        };

        $object2 = clone $object;
        $object2->id = 1;

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNameFromModel')->andReturn('test-table');
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(1)->once();
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(1)->once();
        });

        $relatedModels = app(ModelsRelatedValidatorAndGrouper::class)->validateAndGroup([$object, $object2], $object::class);

        $this->assertEquals(1, count($relatedModels));
        $this->assertEquals($object, $relatedModels[0]);
    }

    public function testTwoException()
    {
        $object = new class {
            public $id = 1;
        };

        $object2 = new class {
            public $id = 2;
        };

        $this->mock(ModelMapper::class, function (MockInterface $mock) {
            $mock->allows('fetchTableNameFromModel')->andReturn('test-table');
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(1)->once();
            $mock->allows('fetchIdentifierValueFromModel')->andReturn(2)->once();
        });

        $this->expectException(DocumentCreatorException::class);
        $this->expectExceptionMessage('Related model is not instanceof source className="'.$object::class.'"');
        app(ModelsRelatedValidatorAndGrouper::class)->validateAndGroup([$object, $object2], $object::class);
    }
}
