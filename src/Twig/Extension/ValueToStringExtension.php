<?php

namespace PimcoreContentMigration\Twig\Extension;

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
    public function valueToString(mixed $value): string
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
        if (is_integer($value)) {
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
                $arrayString .= '            \'' . $key . '\' => ' . $this->valueToString($item) . ",\n";
            }
            $arrayString .= "        ]";
            return $arrayString;
        }

        // OBJECT
        if (is_object($value)) {
            throw new \InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // RESOURCE (z. B. Datei-Handle)
        if (is_resource($value)) {
            throw new \InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // CALLABLE
        if (is_callable($value)) {
            throw new \InvalidArgumentException('Unsupported value type: ' . gettype($value));
        }

        // EVERYTHING ELSE (should not happen)
        throw new \InvalidArgumentException('Unsupported value type: ' . gettype($value));
    }
}
