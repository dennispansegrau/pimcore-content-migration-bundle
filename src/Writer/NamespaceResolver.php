<?php

namespace PimcoreContentMigration\Writer;

use Doctrine\Migrations\DependencyFactory;

use function reset;

use RuntimeException;

readonly class NamespaceResolver
{
    public function __construct(
        private DependencyFactory $dependencyFactory,
    ) {
    }

    public function resolve(?string $namespace): string
    {
        $configuration = $this->dependencyFactory->getConfiguration();
        $migrationDirectories = $configuration->getMigrationDirectories();

        if (empty($namespace)) {
            $directory = reset($migrationDirectories);
            if (empty($directory)) {
                throw new RuntimeException('No namespace defined');
            }
            return $directory;
        }

        if (!isset($migrationDirectories[$namespace])) {
            throw new RuntimeException("Migration path '{$namespace}' does not exist in configuration.");
        }

        return $migrationDirectories[$namespace];
    }
}
