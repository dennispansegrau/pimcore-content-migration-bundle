<?php

namespace PimcoreContentMigration\Generator\Dependency;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;

readonly class Dependency
{
    private string $type;
    private int $id;

    public function __construct(
        private AbstractElement $target,
        private string $variableName,
        private string $methodName,
        private ?string $code
    ) {
        if ($this->target instanceof Document) {
            $this->type = 'document';
        } elseif ($this->target instanceof Asset) {
            $this->type = 'asset';
        } elseif ($this->target instanceof DataObject) {
            $this->type = 'object';
        } else {
            throw new \LogicException('Unknown element type');
        }

        $this->id = $this->target->getId();
    }

    public function getTarget(): AbstractElement
    {
        return $this->target;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
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
