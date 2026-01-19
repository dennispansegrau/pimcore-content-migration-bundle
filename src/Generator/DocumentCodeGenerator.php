<?php

namespace PimcoreContentMigration\Generator;

use function class_exists;
use function get_class;

use InvalidArgumentException;

use function is_string;

use LogicException;
use Pimcore\Bundle\NewsletterBundle\Model\Document\Newsletter;
use Pimcore\Bundle\PersonalizationBundle\Model\Document\Page;
use Pimcore\Bundle\PersonalizationBundle\Model\Document\Snippet;
use Pimcore\Bundle\PersonalizationBundle\Model\Document\Targeting\TargetingDocumentInterface;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\Printcontainer;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\Printpage;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Builder\Document\DocumentBuilder;
use PimcoreContentMigration\Builder\Document\EmailBuilder;
use PimcoreContentMigration\Builder\Document\FolderBuilder;
use PimcoreContentMigration\Builder\Document\HardLinkBuilder;
use PimcoreContentMigration\Builder\Document\LinkBuilder;
use PimcoreContentMigration\Builder\Document\NewsletterBuilder;
use PimcoreContentMigration\Builder\Document\PageBuilder;
use PimcoreContentMigration\Builder\Document\PrintContainerBuilder;
use PimcoreContentMigration\Builder\Document\PrintPageBuilder;
use PimcoreContentMigration\Builder\Document\SnippetBuilder;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Generator\Dependency\DependencyCollector;
use PimcoreContentMigration\Writer\HtmlWriter;
use PimcoreContentMigration\Writer\RelativePath;

class DocumentCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly CodeGenerator $codeGenerator,
        private readonly HtmlWriter $htmlWriter,
        public DependencyCollector $dependencyCollector,
        private readonly AbstractElementToMethodNameConverter $methodNameConverter,
    ) {
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
            'classname' => '\\' . get_class($abstractElement),
            'type' => $abstractElement->getType(),
            'methodName' => $methodName,
            'settings' => $settings,
            'editables' => $this->getEditables($abstractElement, $settings),
            'isPageSnippet' => $abstractElement instanceof Document\PageSnippet,
            'isPrintAbstract' => $abstractElement instanceof PrintAbstract,
            'dependencies' => $this->dependencyCollector->getDependencies($settings, $abstractElement, $existingMethodNames),
            'builder' => $this->getBuilderClass($abstractElement),
            'isImplementingTargetingDocumentInterface' => $abstractElement instanceof TargetingDocumentInterface,
        ]);
    }

    private function getBuilderClass(Document $document): ?string
    {
        if (class_exists(Printpage::class) && $document instanceof Printpage) {
            return '\\' . PrintPageBuilder::class;
        }

        if (class_exists(Printcontainer::class) && $document instanceof Printcontainer) {
            return '\\' . PrintContainerBuilder::class;
        }

        if (class_exists(Newsletter::class) && $document instanceof Newsletter) {
            return '\\' . NewsletterBuilder::class;
        }
        if (class_exists(Page::class) && $document instanceof Page) {
            return '\\' . \PimcoreContentMigration\Builder\Document\Personalization\PageBuilder::class;
        }

        if (class_exists(Snippet::class) && $document instanceof Snippet) {
            return '\\' . \PimcoreContentMigration\Builder\Document\Personalization\SnippetBuilder::class;
        }

        if ($document instanceof Document\Email) {
            return '\\' . EmailBuilder::class ;
        }

        if ($document instanceof Document\Folder) {
            return '\\' . FolderBuilder::class;
        }

        if ($document instanceof Document\Hardlink) {
            return '\\' . HardLinkBuilder::class;
        }

        if ($document instanceof Document\Link) {
            return '\\' . LinkBuilder::class;
        }

        if ($document instanceof Document\Page) {
            return '\\' . PageBuilder::class;
        }

        if ($document instanceof Document\Snippet) {
            return '\\' . SnippetBuilder::class;
        }

        if (get_class($document) === Document::class) {
            return '\\' . DocumentBuilder::class;
        }

        return null;
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
                $relativePath = $this->htmlWriter->write($object, $settings->getNamespace(), $key, (string) $data);
                $relativePath->setEditable($editable);
                $editable = $relativePath;
            }
        }

        /** @phpstan-ignore return.type */
        return $editables;
    }
}
