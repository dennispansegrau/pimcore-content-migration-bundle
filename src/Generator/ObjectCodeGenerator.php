<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\DataObject;

class ObjectCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
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
