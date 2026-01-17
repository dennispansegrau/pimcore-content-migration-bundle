<?php

namespace PimcoreContentMigration\Converter\Stringifier;

use function gettype;

use InvalidArgumentException;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

final readonly class ValueToStringConverter
{
    /**
     * @param ValueStringifier[] $handlers
     */
    public function __construct(
        private array $handlers
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function convertValueToString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($value, $parameters)) {
                return $handler->toString($value, $dependencyList, $parameters);
            }
        }

        throw new InvalidArgumentException('No stringifier found for value type: ' . gettype($value));
    }
}
