<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Factory;

use DennisPansegrau\PimcoreContentMigrationBundle\Generator\AssetCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\CodeGeneratorInterface;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\DocumentCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\ObjectCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\MigrationType;

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
