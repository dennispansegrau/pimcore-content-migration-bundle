<?php

namespace PimcoreContentMigration\Generator\Setter;

use function array_key_first;
use function get_resource_type;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_resource;
use function is_string;

use Pimcore\Model\DataObject\Concrete;

use function reset;

use RuntimeException;

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
}
