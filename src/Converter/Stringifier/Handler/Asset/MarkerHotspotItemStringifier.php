<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Asset;

use function in_array;
use function is_int;

use LogicException;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IdToDependencyStringTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class MarkerHotspotItemStringifier implements ValueStringifier
{
    use IdToDependencyStringTrait;
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof MarkerHotspotItem;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var MarkerHotspotItem $value */
        if (!in_array($value->getType(), ['document', 'asset', 'object'], true)) {
            return $this->getConverter()->valueToString($value->getValue(), $dependencyList);
        }

        $id = $value->getValue();

        if (!is_int($id)) {
            throw new LogicException('Invalid value type in MarkerHotspotItem.');
        }

        return $this->idToDependencyString($value->getType(), $id, $dependencyList, false);
    }
}
