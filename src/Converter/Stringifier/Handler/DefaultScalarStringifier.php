<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler;

use function get_class;
use function gettype;

use InvalidArgumentException;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\IndentTrait;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_repeat;
use function str_replace;

final class DefaultScalarStringifier implements ValueStringifier
{
    use IndentTrait;
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return true; // must be last by priority
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_string($value)) {
            $escaped = str_replace('\'', '\\\'', $value);
            return '\'' . $escaped . '\'';
        }

        if (is_array($value)) {
            return $this->renderArray($value, $dependencyList, $parameters);
        }

        if ($value instanceof AbstractElement) {
            return $this->renderAbstractElement($value, $dependencyList);
        }

        if (is_object($value)) {
            throw new InvalidArgumentException('Unsupported object of class: ' . get_class($value));
        }

        throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
    }

    /**
     * @param array<mixed> $value
     * @param array<string, mixed> $parameters
     */
    private function renderArray(array $value, DependencyList $dependencyList, array $parameters): string
    {
        if ($value === []) {
            return '[]';
        }

        $indent = $this->getIndent($parameters);
        $result = "[\n";

        foreach ($value as $key => $item) {
            $keyString = is_int($key) ? (string) $key : '\'' . str_replace('\'', '\\\'', (string) $key) . '\'';
            $itemString = $this->getConverter()->convertValueToString($item, $dependencyList, ['indent' => $indent + 4]);
            $result .= str_repeat(' ', $indent + 4) . $keyString . ' => ' . $itemString . ",\n";
        }

        return $result . str_repeat(' ', $indent) . ']';
    }

    private function renderAbstractElement(AbstractElement $value, DependencyList $dependencyList): string
    {
        $dependency = $dependencyList->getDependency($value);

        if (!$dependency instanceof Dependency) {
            return '\\' . get_class($value) . '::getByPath(\'' . $value->getFullPath() . '\')';
        }

        return '$' . $dependency->getVariableName();
    }
}
