<?php

declare(strict_types=1);

namespace PimcoreContentMigration\Tests\Unit\Generator\Setter;

use PHPUnit\Framework\TestCase;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use PimcoreContentMigration\Generator\Setter\Setter;
use RuntimeException;
use stdClass;

use function fclose;
use function fopen;

final class SetterTest extends TestCase
{
    public function testItExposesNameAndValue(): void
    {
        $setter = new Setter('field', 'value');

        self::assertSame('field', $setter->getName());
        self::assertSame('value', $setter->getValue());
    }

    public function testItDetectsScalarAndNullTypes(): void
    {
        self::assertSame('null', (new Setter('field', null))->getType());
        self::assertSame('bool', (new Setter('field', true))->getType());
        self::assertSame('int', (new Setter('field', 12))->getType());
        self::assertSame('float', (new Setter('field', 1.5))->getType());
        self::assertSame('string', (new Setter('field', 'value'))->getType());
    }

    public function testItDetectsComplexTypes(): void
    {
        $object = new stdClass();
        $resource = fopen('php://memory', 'rb');

        self::assertSame('array of int', (new Setter('field', [1, 2]))->getType());
        self::assertSame(stdClass::class, (new Setter('field', $object))->getType());
        self::assertSame('stream', (new Setter('field', $resource))->getType());

        fclose($resource);
    }

    public function testItValidatesLevelTwoOnlyForArrays(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('level 2 is only valid for arrays');

        (new Setter('field', 'value'))->getType(2);
    }

    public function testItDetectsConcreteAndFieldcollectionValues(): void
    {
        $concrete = new Concrete(1, '/object');
        $fieldcollection = new Fieldcollection();

        self::assertTrue((new Setter('field', $concrete))->isConcrete());
        self::assertFalse((new Setter('field', $fieldcollection))->isConcrete());
        self::assertTrue((new Setter('field', $fieldcollection))->isFieldcollection());
        self::assertTrue((new Setter('field', [$concrete]))->isConcreteList());
        self::assertFalse((new Setter('field', []))->isConcreteList());
        self::assertFalse((new Setter('field', 'not-an-array'))->isConcreteList());
        self::assertFalse((new Setter('field', ['x']))->isConcreteList());
    }

    public function testItBuildsSafeVariableNames(): void
    {
        $setter = new Setter('12 invalid-name', 'value');

        self::assertSame('$_12_invalid_name', $setter->getSafeVariableName());
        self::assertSame('__12_invalid_nameSuffix', $setter->getSafeVariableName('_', 'Suffix'));
    }
}
