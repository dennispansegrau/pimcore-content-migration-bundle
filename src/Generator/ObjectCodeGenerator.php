<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\DataObject;

class ObjectCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @implements CodeGeneratorInterface<DataObject>
     */
    public function generateCode(object $object, Settings $settings): string
    {
        return '// Hallo Welt';
    }
}
