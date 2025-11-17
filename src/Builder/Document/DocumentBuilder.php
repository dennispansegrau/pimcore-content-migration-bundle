<?php

namespace PimcoreContentMigration\Builder\Document;

use PimcoreContentMigration\Builder\Builder;
use function basename;
use function dirname;

use Exception;
use LogicException;
use Pimcore\Model\Document;
use Pimcore\Model\Element\DuplicateFullPathException;

class DocumentBuilder extends Builder
{
    protected ?Document $document = null;

    protected static function getDocumentClass(): string
    {
        return Document::class;
    }

    /**
     * @throws Exception
     */
    public static function createOrUpdate(string $path): static
    {
        $builder = new static();
        /** @var class-string<Document> $documentClass */
        $documentClass = static::getDocumentClass();

        $builder->document = $documentClass::getByPath($path);
        if (!$builder->document instanceof $documentClass) {
            $builder->document = new $documentClass();
            $key = basename($path);
            $builder->document->setKey($key);
            $parentPath = dirname($path);
            $parent = Document::getByPath($parentPath);
            if (!$parent instanceof Document) {
                throw new Exception("Parent document not found for path: $parentPath");
            }
            $builder->document->setParentId($parent->getId());
            $builder->document->save(); // must be already saved for some actions
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

    public function setProperty(
        string $name,
        string $type,
        mixed $data,
        bool $inherited = false,
        bool $inheritable = false
    ): static {
        $this->getObject()->setProperty($name, $type, $data, $inherited, $inheritable);
        return $this;
    }

    public function getObject(): Document
    {
        if (null === $this->document) {
            throw new LogicException('Document object has not been set');
        }
        return $this->document;
    }
}
