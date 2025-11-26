<?php

namespace PimcoreContentMigration\Twig\Extension;

use function get_class;
use function gettype;

use InvalidArgumentException;

use function is_array;
use function is_bool;
use function is_callable;
use function is_float;
use function is_int;
use function is_object;
use function is_resource;
use function is_string;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function str_replace;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ValueToStringExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('value_to_string', [$this, 'valueToString']),
        ];
    }

    /**
     * Converts any value into a readable string.
     */
    public function valueToString(mixed $value, DependencyList $dependencyList): string
    {
        // NULL
        if ($value === null) {
            return 'null';
        }

        // BOOLEAN
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // INTEGER
        if (is_int($value)) {
            return (string) $value;
        }

        // FLOAT
        if (is_float($value)) {
            return (string) $value;
        }

        // STRING
        if (is_string($value)) {
            $value = str_replace('\'', '\\\'', $value);
            return '\'' . $value . '\'';
        }

        // ARRAY
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            $arrayString = "[\n";
            foreach ($value as $key => $item) {
                $arrayString .= '            \'' . $key . '\' => ' . $this->valueToString($item, $dependencyList) . ",\n";
            }
            $arrayString .= '        ]';
            return $arrayString;
        }

        // OBJECT
        if (is_object($value)) {
            $dependency = $dependencyList->getDependency($value);
            if (!$dependency instanceof Dependency) {
                // try to get the element by path if it is not in the dependency list
                if ($value instanceof AbstractElement) {
                    return '\\' . get_class($value) . '::getByPath(\'' . $value->getFullPath() . '\')';
                } else {
                    return 'null';
                }
            }
            return $dependency->getVariableName();
        }

        // RESOURCE (z. B. Datei-Handle)
        if (is_resource($value)) {
            throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // CALLABLE
        if (is_callable($value)) {
            throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // EVERYTHING ELSE (should not happen)
        throw new InvalidArgumentException('Unsupported value type: ' . gettype($value));
    }
}
