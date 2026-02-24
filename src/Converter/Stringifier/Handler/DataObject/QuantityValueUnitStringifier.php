<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\QuantityValue\Unit;
use PimcoreContentMigration\Builder\DataObject\QuantityValue\UnitBuilder;
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
        $builderClass = UnitBuilder::class;

        $id = $value->getId();
        $abbreviation = $value->getAbbreviation();
        $longName = $value->getLongName();
        $baseUnit = $value->getBaseunit();
        $factor = $value->getFactor();
        $converterOffset = $value->getConversionOffset();
        $converter = $value->getConverter();

        $idString = $this->getConverter()->convertValueToString($id, $dependencyList, $parameters);
        $abbreviationString = $this->getConverter()->convertValueToString($abbreviation, $dependencyList, $parameters);
        $longNameString = $this->getConverter()->convertValueToString($longName, $dependencyList, $parameters);
        $baseUnitString = $this->getConverter()->convertValueToString($baseUnit, $dependencyList, $parameters);
        $factorString = $this->getConverter()->convertValueToString($factor, $dependencyList, $parameters);
        $converterOffsetString = $this->getConverter()->convertValueToString($converterOffset, $dependencyList, $parameters);
        $converterString = $this->getConverter()->convertValueToString($converter, $dependencyList, $parameters);

        return sprintf(
            '\%s::findOrCreate(%s, %s, %s, %s, %s, %s, %s)',
            $builderClass,
            $idString,
            $abbreviationString,
            $longNameString,
            $baseUnitString,
            $factorString,
            $converterOffsetString,
            $converterString
        );
    }
}
