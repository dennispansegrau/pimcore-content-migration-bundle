<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler;

use function is_string;

use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_starts_with;

final class NewExpressionStringifier implements ValueStringifier
{
    public function supports(mixed $value, array $parameters = []): bool
    {
        return is_string($value) && str_starts_with($value, 'new \\');
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var string $value */
        return $value;
    }
}
