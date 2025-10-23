<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Factory;

use DennisPansegrau\PimcoreContentMigrationBundle\Generator\AssetCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\CodeGeneratorInterface;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\DocumentCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\Generator\ObjectCodeGenerator;
use DennisPansegrau\PimcoreContentMigrationBundle\MigrationType;

class CodeGeneratorFactory implements CodeGeneratorFactoryInterface
{
    public function getCodeGenerator(string $type): CodeGeneratorInterface
    {
        return match ($type) {
            MigrationType::DOCUMENT->value => new DocumentCodeGenerator(),
            MigrationType::ASSET->value => new AssetCodeGenerator(),
            MigrationType::OBJECT->value => new ObjectCodeGenerator(),
            default => throw new \RuntimeException(\sprintf('Unsupported type "%s".', $type)),
        };
    }
}
