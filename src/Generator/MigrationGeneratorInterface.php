<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Element\AbstractElement;

interface MigrationGeneratorInterface
{
    public function generateMigrationFile(AbstractElement $object, string $methodCode, Settings $settings): string;
}
