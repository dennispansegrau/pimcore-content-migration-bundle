<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Carbon\CarbonPeriod;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final readonly class CarbonPeriodStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof CarbonPeriod;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var CarbonPeriod $value */
        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();

        $startDateString = $this->getConverter()->convertValueToString($startDate, $dependencyList, $parameters);
        $endDateString = $this->getConverter()->convertValueToString($endDate, $dependencyList, $parameters);

        return sprintf('\Carbon\CarbonPeriod::create(%s, %s)', $startDateString, $endDateString);
    }
}
