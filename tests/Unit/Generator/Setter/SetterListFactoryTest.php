<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator\Setter;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue;
use Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation;
use Pimcore\Model\DataObject\Concrete;
use PimcoreContentMigration\Generator\Setter\SetterListFactory;
use stdClass;

use function iterator_to_array;

final class SetterListFactoryTest extends TestCase
{
    public function testItReturnsEmptyListForNonConcreteObjects(): void
    {
        $list = (new SetterListFactory())->getList(new DataObject(1, '/object'));

        self::assertCount(0, $list);
    }

    public function testItBuildsSettersAndSkipsUnsupportedFieldDefinitions(): void
    {
        $object = new class (1, '/product') extends Concrete {
            public function getTitle(): string
            {
                return 'Demo';
            }

            public function getPrice(): float
            {
                return 9.99;
            }
        };

        $object->setClass(new ClassDefinition([
            'title' => new stdClass(),
            'calculatedField' => new CalculatedValue(),
            'reverseRelation' => new ReverseObjectRelation(),
            'price' => new stdClass(),
        ]));

        $list = (new SetterListFactory())->getList($object);
        $items = iterator_to_array($list);

        self::assertCount(2, $items);
        self::assertSame('title', $items[0]->getName());
        self::assertSame('Demo', $items[0]->getValue());
        self::assertSame('price', $items[1]->getName());
        self::assertSame(9.99, $items[1]->getValue());
    }
}
