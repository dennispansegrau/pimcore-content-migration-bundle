<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Generator;

use Pimcore\Model\Document;

readonly class DocumentCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private CodeGenerator $codeGenerator,
    ) {
    }

    /**
     * @implements CodeGeneratorInterface<Document>
     */
    public function generateCode(object $object): string
    {
        return $this->codeGenerator->generate('document_template', [
            'object' => $object,
        ]);
    }
}
