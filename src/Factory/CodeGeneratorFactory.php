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

    public function getCodeGenerator(MigrationType $type): CodeGeneratorInterface
    {
        return match ($type) {
            MigrationType::DOCUMENT => $this->documentCodeGenerator,
            MigrationType::ASSET => $this->assetCodeGenerator,
            MigrationType::OBJECT => $this->objectCodeGenerator,
            default => throw new \RuntimeException(\sprintf('Unsupported type "%s".', $type->value)),
        };
    }
}
