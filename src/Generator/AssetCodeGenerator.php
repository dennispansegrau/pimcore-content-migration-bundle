<?php

namespace PimcoreContentMigration\Generator;

class AssetCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @param object $abstractElement
     * @param Settings $settings
     * @param string[] $existingMethodNames
     * @return string
     */
    public function generateCode(object $abstractElement, Settings $settings, array &$existingMethodNames = []): string
    {
        return '// Hallo Welt';
    }
}
