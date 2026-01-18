<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\Geobounds;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

class GeoboundsStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Geobounds;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Geobounds $value */
        $northEast = $value->getNorthEast();
        $southWest = $value->getSouthWest();
        $northEastString = $this->getConverter()->convertValueToString($northEast, $dependencyList, $parameters);
        $southWestString = $this->getConverter()->convertValueToString($southWest, $dependencyList, $parameters);
        return sprintf('new \Pimcore\Model\DataObject\Data\Geobounds(%s, %s)', $northEastString, $southWestString);
    }
}
