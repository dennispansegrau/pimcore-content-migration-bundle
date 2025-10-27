<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Asset;

class AssetCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @implements CodeGeneratorInterface<Asset>
     */
    public function generateCode(object $object): string
    {
        return '// Hallo Welt';
    }
}
