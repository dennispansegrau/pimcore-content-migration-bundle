<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\QuantityValueRange;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class QuantityValueRangeStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof QuantityValueRange;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var QuantityValueRange $value */
        $maximum = $value->getMaximum();
        $minimum = $value->getMinimum();
        $unit = $value->getUnit();

        $maximumString = $this->getConverter()->convertValueToString($maximum, $dependencyList, $parameters);
        $minimumString = $this->getConverter()->convertValueToString($minimum, $dependencyList, $parameters);
        $unitString = $this->getConverter()->convertValueToString($unit, $dependencyList, $parameters);

        return sprintf('\Pimcore\Model\DataObject\Data\QuantityValueRange(%s, %s, %s)', $maximumString, $minimumString, $unitString);
    }
}
