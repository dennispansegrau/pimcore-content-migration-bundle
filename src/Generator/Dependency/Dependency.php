<?php

namespace PimcoreContentMigration\Generator\Dependency;

readonly class Dependency
{
    public function __construct(
        private object $target,
        private string $type,
        private string $variableName,
        private string $methodName,
        private ?string $code
    ) {
    }

    public function getTarget(): object
    {
        return $this->target;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
