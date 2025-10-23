<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Generator;

use Pimcore\Model\Document;

class DocumentCodeGenerator implements CodeGeneratorInterface
{
    /**
     * @implements CodeGeneratorInterface<Document>
     */
    public function generateCode(object $object): string
    {
        return '// Hallo Welt';
    }
}
