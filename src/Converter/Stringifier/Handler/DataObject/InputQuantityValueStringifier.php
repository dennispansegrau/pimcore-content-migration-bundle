<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\InputQuantityValue;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class InputQuantityValueStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof InputQuantityValue;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var InputQuantityValue $value */
        $qValue = $value->getValue();
        $unit = $value->getUnit();

        $qValueString = $this->getConverter()->convertValueToString($qValue, $dependencyList, $parameters);
        $unitString = $this->getConverter()->convertValueToString($unit, $dependencyList, $parameters);
        return sprintf('new \Pimcore\Model\DataObject\Data\InputQuantityValue(%s, %s)', $qValueString, $unitString);
    }
}
