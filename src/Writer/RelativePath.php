<?php

namespace PimcoreContentMigration\Writer;

use Pimcore\Model\Document\Editable;

final class RelativePath
{
    private ?Editable $editable = null;

    public function __construct(
        private readonly string $name,
        private readonly string $relativePath,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setEditable(Editable $editable): void
    {
        $this->editable = $editable;
    }

    public function getEditable(): ?Editable
    {
        return $this->editable;
    }
}
