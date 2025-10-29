<?php

namespace PimcoreContentMigration\Generator;

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
        if (!$object instanceof Document\Email) {
            throw new \InvalidArgumentException();
        }

//        dd($object->setEditables());

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'type' => $object->getType(),
        ]);
    }
}
