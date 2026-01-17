<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\Trait;

use function is_numeric;

trait IndentTrait
{
    /**
     * @param array<string, mixed> $parameters
     */
    private function getAndIncreaseIndent(array &$parameters, int $default = 12): int
    {
        $indent = is_numeric($parameters['indent'] ?? null)
            ? (int) $parameters['indent']
            : $default;

        $parameters['indent'] = $indent + 4;

        return $indent;
    }
}
