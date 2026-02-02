<?php

namespace PimcoreContentMigration\Builder\Document;

use function basename;
use function dirname;

use Exception;
use LogicException;
use Pimcore\Model\Document;
use Pimcore\Model\Element\DuplicateFullPathException;
use PimcoreContentMigration\Builder\AbstractElementBuilder;

class DocumentBuilder extends AbstractElementBuilder
{
    protected ?Document $document = null;

    protected static function getDocumentClass(): string
    {
        return Document::class;
    }

    /**
     * @throws Exception
     */
    public static function findOrCreate(string $path): static
    {
        $builder = new static();
        /** @var class-string<Document> $documentClass */
        $documentClass = static::getDocumentClass();

        $builder->document = Document::getByPath($path);
        if (!$builder->document instanceof Document) {
            $parentPath = dirname($path);
            $key = basename($path);
            $builder->document = $builder->createDocument($documentClass, $parentPath, $key);
        }

        // document already exists but is not of the correct type
        if (!$builder->document instanceof $documentClass) {
            $parentPath = dirname($path);
            $tempKey = 'temp_'. basename($path) . '_' . random_int(1000, 9999);
            try {
                $tempObject = $builder->createDocument($documentClass, $parentPath, $tempKey);
                $builder->replaceDocument($builder->document, $tempObject);
            } catch (Exception $exception) {
                $tempObject = Document::getByPath($parentPath . '/' . $tempKey);
                $tempObject?->delete();
            }
        }

        return $builder;
    }

    public function setPublished(bool $published): static
    {
        $this->getObject()->setPublished($published);
        return $this;
    }

    /**
     * @param array<string, string> $parameters
     * @return $this
     * @throws DuplicateFullPathException
     */
    public function save(array $parameters = []): static
    {
        $this->getObject()->save($parameters);
        return $this;
    }

    public function setIndex(int $index): static
    {
        $this->getObject()->setIndex($index);
        return $this;
    }

    public function setType(string $type): static
    {
        $this->getObject()->setType($type);
        return $this;
    }

    public function getObject(): Document
    {
        if (null === $this->document) {
            throw new LogicException('Document object has not been set');
        }
        return $this->document;
    }

    /**
     * @throws Exception
     */
    private function getParentByPath(string $parentPath): Document
    {
        $parent = null;
        if (Document\Service::pathExists($parentPath)) {
            $parent = Document::getByPath($parentPath);
        }

        if ($parent === null) {
            $parent = Document\Service::createFolderByPath($parentPath);
        }

        if (!$parent instanceof Document) {
            throw new Exception("Parent document not found for path: $parentPath");
        }

        return $parent;
    }

    /**
     * @throws Exception
     */
    private function createDocument(string $documentClass, string $parentPath, string $key): Document
    {
        $document = new $documentClass();
        if (!$document instanceof Document) {
            throw new Exception("Class $documentClass is not a Document");
        }
        $document->setKey(Document\Service::getValidKey($key, 'document'));
        $parent = $this->getParentByPath($parentPath);
        $document->setParent($parent);
        $document->save();
        return $document;
    }

    /**
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    private function replaceDocument(Document $oldDocument, Document $newDocument): void
    {
        $children = $oldDocument->getChildren();
        foreach ($children as $child) {
            if (!$child instanceof Document) {
                continue;
            }
            $child->setParent($newDocument);
            $child->save();
        }

        $oldKey = $oldDocument->getKey();
        $oldDocument->delete();

        if ($oldKey === null) {
            throw new LogicException('Old document has no key');
        }
        $oldDocument->setKey($oldKey);
        $newDocument->save();

        $this->document = $newDocument;
    }
}
