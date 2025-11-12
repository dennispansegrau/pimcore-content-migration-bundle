<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Asset;

class AssetCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @implements CodeGeneratorInterface<Asset>
     */
    public function generateCode(object $object, Settings $settings, array &$existingMethodNames = []): string
    {
        return '// Hallo Welt';
    }
}
