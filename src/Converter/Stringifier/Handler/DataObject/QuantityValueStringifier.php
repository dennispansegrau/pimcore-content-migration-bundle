<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use InvalidArgumentException;
use Pimcore\Model\DataObject\Data\QuantityValue;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final readonly class QuantityValueStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof QuantityValue;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var QuantityValue $value */
        $quantityValue = $value->getValue();
        $unitId = $value->getUnit()?->getId();
        if ($unitId === null) {
            throw new InvalidArgumentException('QuantityValue must have a unit with an id.');
        }

        $quantityValueString = $this->getConverter()->valueToString($quantityValue, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\QuantityValue(%s, \'%s\')', $quantityValueString, $unitId);
    }
}
