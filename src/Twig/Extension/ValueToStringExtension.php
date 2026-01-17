<?php

namespace PimcoreContentMigration\Twig\Extension;

use PimcoreContentMigration\Converter\Stringifier\ValueToStringConverter;
use PimcoreContentMigration\Generator\Dependency\DependencyList;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ValueToStringExtension extends AbstractExtension
{
    public function __construct(
        private readonly ValueToStringConverter $converter,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pcmb_value_to_string', [$this, 'valueToString']),
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     * Converts any value into a readable string.
     */
    public function valueToString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        return $this->converter->convertValueToString($value, $dependencyList, $parameters);
    }
}
