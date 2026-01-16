<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Carbon\Carbon;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final readonly class CarbonStringifier implements ValueStringifier
{
    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Carbon;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Carbon $value */
        $time = $value->toDateTimeString();
        $timezone = $value->getTimezone()->getName();

        return sprintf('new \Carbon\Carbon(\'%s\', \'%s\')', $time, $timezone);
    }
}
