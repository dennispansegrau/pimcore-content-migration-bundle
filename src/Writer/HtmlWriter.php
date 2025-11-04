<?php

namespace PimcoreContentMigration\Writer;

use Doctrine\Migrations\DependencyFactory;
use Pimcore\Model\Element\AbstractElement;

readonly class HtmlWriter implements WriterInterface
{
    private const PREFIX = '/files/documents';
    private const POSTFIX = '.wysiwyg.html';

    public function __construct(
        private DependencyFactory $dependencyFactory,
    ) {
    }

    public function write(AbstractElement $object, string $migrationNamespace, string $fileName, string $data): RelativePath
    {
        $relativePath = new RelativePath($fileName, self::PREFIX . $object->getFullPath() . '/' . $fileName . self::POSTFIX);
        $absolutePath = $this->getMigrationPath($migrationNamespace) . $relativePath->getRelativePath();
        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        if (file_put_contents($absolutePath, $data) === false) {
            throw new \RuntimeException("Failed to write to path: $absolutePath");
        }
        return $relativePath;
    }

    private function getMigrationPath(string $namespace): string
    {
        $configuration = $this->dependencyFactory->getConfiguration();
        $migrationDirectories = $configuration->getMigrationDirectories();

        if (!isset($migrationDirectories[$namespace])) {
            throw new \RuntimeException("Migration path '{$namespace}' does not exist in configuration.");
        }

        return $migrationDirectories[$namespace];
    }
}
