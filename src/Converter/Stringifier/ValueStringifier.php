<?php

namespace PimcoreContentMigration\Converter\Stringifier;

use PimcoreContentMigration\Generator\Dependency\DependencyList;

interface ValueStringifier
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function supports(mixed $value, array $parameters = []): bool;

    /**
     * Converts any value into a readable string.
     * @param array<string, mixed> $parameters
     */
    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string;
}
