<?php

namespace PimcoreContentMigration\Generator\Setter;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use RuntimeException;

use function array_key_first;
use function get_resource_type;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_resource;
use function is_string;
use function preg_match;
use function preg_replace;
use function reset;

readonly class Setter
{
    public function __construct(
        private string $name,
        private mixed $value,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(int $level = 1): string
    {
        $value = $this->value;
        if ($level === 2) {
            if (!is_array($value)) {
                throw new RuntimeException('level 2 is only valid for arrays');
            }
            $value = reset($value);
        }

        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return 'bool';
        }

        if (is_int($value)) {
            return 'int';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_string($value)) {
            return 'string';
        }

        if (is_array($value)) {
            return 'array of ' . $this->getType(2);
        }

        if (is_object($value)) {
            return $value::class;
        }

        if (is_resource($value)) {
            return get_resource_type($value);
        }

        return 'unknown';
    }

    public function isConcreteList(): bool
    {
        return is_array($this->value)
            && $this->value !== []
            && $this->value[array_key_first($this->value)] instanceof Concrete;
    }

    public function isConcrete(): bool
    {
        return $this->value instanceof Concrete;
    }

    public function isFieldcollection(): bool
    {
        return $this->value instanceof Fieldcollection;
    }

    /**
     * Returns a variable name that is safe to use in PHP code.
     */
    public function getSafeVariableName(string $prefix = '$', string $postfix = ''): string
    {
        $varName = preg_replace('/[^a-zA-Z0-9_]/', '_', $this->name);
        if (!is_string($varName)) {
            throw new RuntimeException('Invalid variable name');
        }
        if (!preg_match('/^[a-zA-Z_]/', $varName)) {
            $varName = '_' . $varName;
        }
        return $prefix . $varName . $postfix;
    }
}
