<?php

namespace PimcoreContentMigration\Factory;

use PimcoreContentMigration\Generator\AssetCodeGenerator;
use PimcoreContentMigration\Generator\CodeGeneratorInterface;
use PimcoreContentMigration\Generator\DocumentCodeGenerator;
use PimcoreContentMigration\Generator\ObjectCodeGenerator;
use PimcoreContentMigration\MigrationType;

readonly class CodeGeneratorFactory implements CodeGeneratorFactoryInterface
{
    public function __construct(
        private DocumentCodeGenerator $documentCodeGenerator,
        private AssetCodeGenerator $assetCodeGenerator,
        private ObjectCodeGenerator $objectCodeGenerator,
    ) {
    }

    public function getCodeGenerator(string $type): CodeGeneratorInterface
    {
        return match ($type) {
            MigrationType::DOCUMENT->value => $this->documentCodeGenerator,
            MigrationType::ASSET->value => $this->assetCodeGenerator,
            MigrationType::OBJECT->value => $this->objectCodeGenerator,
            default => throw new \RuntimeException(\sprintf('Unsupported type "%s".', $type)),
        };
    }
}
