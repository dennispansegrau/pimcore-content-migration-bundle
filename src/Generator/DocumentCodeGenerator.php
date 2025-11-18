<?php

namespace PimcoreContentMigration\Generator;

use InvalidArgumentException;

use function is_string;

use LogicException;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\Writer\HtmlWriter;
use PimcoreContentMigration\Writer\RelativePath;

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

    public function generateCode(AbstractElement $abstractElement, Settings $settings, array &$existingMethodNames = []): string
    {
        if (!$abstractElement instanceof Document) {
            throw new InvalidArgumentException();
        }

        $methodName = $this->methodNameConverter->convert($abstractElement);
        if (empty($existingMethodNames)) {
            $existingMethodNames[] = $methodName;
        }

        return $this->codeGenerator->generate('document_template', [
            'document' => $abstractElement,
            'type' => $abstractElement->getType(),
            'methodName' => $methodName,
            'settings' => $settings,
            'editables' => $this->getEditables($abstractElement, $settings),
            'isPageSnippet' => $abstractElement instanceof Document\PageSnippet,
            'isPrintAbstract' => $abstractElement instanceof PrintAbstract,
            'dependencies' => $this->getDependencies($settings, $abstractElement, $existingMethodNames),
        ]);
    }

    /**
     * @param Document $object
     * @param Settings $settings
     * @return array<string, Document\Editable\EditableInterface|RelativePath>
     */
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
                $data = $editable->getData();
                if (!is_string($data) && $data !== null) {
                    throw new LogicException('Wysiwyg editable data must be a string or null.');
                }
                $editable = $this->htmlWriter->write($object, $settings->getNamespace(), $key, (string) $data);
            }
        }

        /** @phpstan-ignore return.type */
        return $editables;
    }
}
