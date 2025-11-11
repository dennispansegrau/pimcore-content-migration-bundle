<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;
use Pimcore\Model\Document;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Writer\HtmlWriter;

readonly class DocumentCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private CodeGenerator $codeGenerator,
        private HtmlWriter $htmlWriter,
        private AbstractElementToMethodNameConverter $methodNameConverter,
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

        if ($settings->withDependencies() && $object->getDependencies()->getRequiresTotalCount() > 0) {
            foreach ($object->getDependencies() as $dependency) {
                // TODO: generate code for each dependency and add it to code[
            }
        }

        if ($settings->withChildren() && $object->getChildAmount() > 0) {
            // TODO: generate code for each children with all their dependencies code
        }

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'type' => $object->getType(),
            'methodName' => $this->methodNameConverter->convert($object),
            'settings' => $settings,
            'editables' => $this->getEditables($object, $settings),
            'isPageSnippet' => $object instanceof Document\PageSnippet,
            'isPrintAbstract' => $object instanceof PrintAbstract,
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
