<?php

namespace PimcoreContentMigration\Writer;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;

use Pimcore\Model\Element\AbstractElement;
use RuntimeException;

readonly class AssetWriter implements WriterInterface
{
    private const PREFIX = '/files/asset';

    public function __construct(
        private NamespaceResolver $namespaceResolver,
    ) {
    }

    public function write(AbstractElement $object, ?string $migrationNamespace, string $fileName, string $data): RelativePath
    {
        $relativePath = new RelativePath($fileName, self::PREFIX . $object->getFullPath());
        $absolutePath = $this->namespaceResolver->resolve($migrationNamespace) . $relativePath->getRelativePath();
        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        if (file_put_contents($absolutePath, $data) === false) {
            throw new RuntimeException("Failed to write to path: $absolutePath");
        }
        return $relativePath;
    }
}
