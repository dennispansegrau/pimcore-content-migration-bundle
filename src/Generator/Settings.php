<?php

namespace PimcoreContentMigration\Generator;

use PimcoreContentMigration\MigrationType;

final readonly class Settings
{
    public function __construct(
        private MigrationType $type,
        private int $id,
        private ?string $namespace = null,
        private bool $inlineWysiwyg = false,
        private bool $withDependencies = true,
        private bool $withChildren = false,
    ) {
    }

    public function getType(): MigrationType
    {
        return $this->type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function inlineWysiwyg(): bool
    {
        return $this->inlineWysiwyg;
    }

    public function withDependencies(): bool
    {
        return $this->withDependencies;
    }

    public function withChildren(): bool
    {
        return $this->withChildren;
    }

    public function forDependencies(): self
    {
        return new self(
            $this->getType(),
            $this->getId(),
            $this->getNamespace(),
            $this->inlineWysiwyg(),
            false,
            false,
        );
    }
}
