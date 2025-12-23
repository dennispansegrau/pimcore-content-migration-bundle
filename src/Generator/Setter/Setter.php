<?php

namespace PimcoreContentMigration\Generator\Setter;

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

    public function isObjectOrListOfObjects(): bool
    {
        if (is_object($this->value)) {
            return true;
        }

        if (!is_array($this->value) || $this->value === []) {
            return false;
        }

        foreach ($this->value as $item) {
            if (!is_object($item)) {
                return false;
            }
        }

        return true;
    }
}
