<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\DataObject;

class ObjectCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @implements CodeGeneratorInterface<DataObject>
     */
    public function generateCode(object $object, Settings $settings, array &$existingMethodNames = []): string
    {
        return '// Hallo Welt';
    }
}
