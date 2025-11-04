<?php

namespace PimcoreContentMigration\Writer;

final readonly class RelativePath
{
    public function __construct(
        private string $name,
        private string $relativePath,
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
}
