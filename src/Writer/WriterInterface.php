<?php

namespace PimcoreContentMigration\Writer;

use Pimcore\Model\Element\AbstractElement;

interface WriterInterface
{
    public function write(AbstractElement $object, string $migrationNamespace, string $fileName, string $data): RelativePath;
}
