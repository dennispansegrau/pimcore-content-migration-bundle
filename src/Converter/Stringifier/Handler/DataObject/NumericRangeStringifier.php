<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\NumericRange;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class NumericRangeStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof NumericRange;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var NumericRange $value */
        $min = $value->getMinimum();
        $max = $value->getMaximum();

        $minString = $this->getConverter()->convertValueToString($min, $dependencyList, $parameters);
        $maxString = $this->getConverter()->convertValueToString($max, $dependencyList, $parameters);
        return sprintf('new \Pimcore\Model\DataObject\Data\NumericRange(%s, %s)', $minString, $maxString);
    }
}
