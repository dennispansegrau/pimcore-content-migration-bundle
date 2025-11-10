<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Document;
use PimcoreContentMigration\Writer\HtmlWriter;

readonly class DocumentCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private CodeGenerator $codeGenerator,
        private HtmlWriter $htmlWriter,
    ) {
    }

    /**
     * @implements CodeGeneratorInterface<Document>
     */
    public function generateCode(object $object, Settings $settings): string
    {
        if (!$object instanceof Document) {
            throw new \InvalidArgumentException();
        }

//        dd($object->setEditables());

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'type' => $object->getType(),
            'settings' => $settings,
            'editables' => $this->getEditables($object, $settings),
        ]);
    }

    private function getEditables(Document $object, Settings $settings): array
    {
        if (!$object instanceof Document\PageSnippet) {
            return [];
        }

        $editables = $object->getEditables();
        if (!$settings->inlineWysiwyg()) {
            foreach ($editables as $key => &$editable) {
                if (!$editable instanceof Document\Editable\Wysiwyg) {
                    continue;
                }
                $editable = $this->htmlWriter->write($object, $settings->getNamespace(), $key, $editable->getData());
            }
        }

        return $editables;
    }
}
