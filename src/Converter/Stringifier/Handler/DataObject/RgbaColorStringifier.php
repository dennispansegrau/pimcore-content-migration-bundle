<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\RgbaColor;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

class RgbaColorStringifier implements ValueStringifier
{
    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof RgbaColor;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var RgbaColor $value */
        return sprintf('new \Pimcore\Model\DataObject\Data\RgbaColor(%d, %d, %d, %d)',
            $value->getR(),
            $value->getG(),
            $value->getB(),
            $value->getA()
        );
    }
}
