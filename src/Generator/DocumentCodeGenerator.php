<?php

namespace PimcoreContentMigration\Generator;

use InvalidArgumentException;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;
use Pimcore\Model\Document;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\Writer\HtmlWriter;

class DocumentCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly CodeGenerator $codeGenerator,
        private readonly HtmlWriter $htmlWriter,
        AbstractElementToMethodNameConverter $methodNameConverter,
        ObjectLoaderInterface $objectLoader
    ) {
        parent::__construct(
            $methodNameConverter,
            $objectLoader
        );
    }

    /**
     * @implements CodeGeneratorInterface<Document>
     */
    public function generateCode(object $object, Settings $settings, array &$existingMethodNames = []): string
    {
        if (!$object instanceof Document) {
            throw new InvalidArgumentException();
        }

        $methodName = $this->methodNameConverter->convert($object);
        if (empty($existingMethodNames)) {
            $existingMethodNames[] = $methodName;
        }

        return $this->codeGenerator->generate('document_template', [
            'document' => $object,
            'type' => $object->getType(),
            'methodName' => $methodName,
            'settings' => $settings,
            'editables' => $this->getEditables($object, $settings),
            'isPageSnippet' => $object instanceof Document\PageSnippet,
            'isPrintAbstract' => $object instanceof PrintAbstract,
            'dependencies' => $this->getDependencies($settings, $object, $existingMethodNames),
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
