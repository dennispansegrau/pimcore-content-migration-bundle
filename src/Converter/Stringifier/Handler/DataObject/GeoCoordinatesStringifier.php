<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\GeoCoordinates;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class GeoCoordinatesStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof GeoCoordinates;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var GeoCoordinates $value */
        $lat = $value->getLatitude();
        $long = $value->getLongitude();

        $latString = $this->getConverter()->valueToString($lat, $dependencyList, $parameters);
        $longString = $this->getConverter()->valueToString($long, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\GeoCoordinates(%s, %s)', $latString, $longString);
    }
}
