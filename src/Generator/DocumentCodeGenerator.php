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
        if (!$object instanceof Document) {
            throw new \InvalidArgumentException();
        }

//        dd($object);

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'builder_path' => $this->getBuilderPath($object->getType()),
        ]);
    }

    private function getBuilderPath(string $type): string
    {
        return match ($type) {
            'email' => '\PimcoreContentMigration\Builder\Document\EmailBuilder',
            'folder' => '\PimcoreContentMigration\Builder\Document\FolderBuilder',
            'hardlink' => '\PimcoreContentMigration\Builder\Document\HardLinkBuilder',
            'link' => '\PimcoreContentMigration\Builder\Document\LinkBuilder',
            'page' => '\PimcoreContentMigration\Builder\Document\PageBuilder',
            'snippet' => '\PimcoreContentMigration\Builder\Document\SnippetBuilder',
            'printpage' => '\PimcoreContentMigration\Builder\Document\PrintPageBuilder',
            'printcontainer' => '\PimcoreContentMigration\Builder\Document\PrintContainerBuilder',
            'newsletter' => '\PimcoreContentMigration\Builder\Document\NewsletterBuilder',
            'document' => '\PimcoreContentMigration\Builder\Document\DocumentBuilder',
            default => throw new \RuntimeException('There is not builder implemented for the document type ' . $type),
        };
    }
}
