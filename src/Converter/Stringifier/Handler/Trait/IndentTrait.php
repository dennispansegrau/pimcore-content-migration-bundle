<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Trait;

use function is_numeric;

trait IndentTrait
{
    /**
     * @param array<string, mixed> $parameters
     */
    private function getIndent(array $parameters, int $default = 12): int
    {
        return is_numeric($parameters['indent'] ?? null)
            ? (int) $parameters['indent']
            : $default;
    }
}
