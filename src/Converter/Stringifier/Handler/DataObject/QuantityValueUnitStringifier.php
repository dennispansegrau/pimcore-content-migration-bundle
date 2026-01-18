<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\QuantityValue\Unit;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class QuantityValueUnitStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Unit;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Unit $value */
        return sprintf('\Pimcore\Model\DataObject\QuantityValue\Unit::getById(\'%s\')', $value->getId() ?? '');
    }
}
