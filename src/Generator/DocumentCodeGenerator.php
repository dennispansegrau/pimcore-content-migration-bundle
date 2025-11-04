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

        $editables = $object->getEditables();
        if (!$settings->inlineWysiwyg()) {
            // TODO: extract all wysiwg content and replace by link
            foreach ($editables as $key => &$editable) {
                if (!$editable instanceof Document\Editable\Wysiwyg) {
                    continue;
                }
                $editable = $this->htmlWriter->write($object, $settings->getNamespace(), $key, $editable->getData());
            }
        }

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'type' => $object->getType(),
            'settings' => $settings,
            'editables' => $editables,
        ]);
    }
}
