<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document;
use Pimcore\Model\Element\DuplicateFullPathException;

class DocumentBuilder
{
    protected ?Document $document = null;

    protected function __construct()
    {
    }

    /**
     * @throws \Exception
     */
    public static function createOrUpdate(string $path): self
    {
        $builder = new self();
        $builder->document = Document::getByPath($path);
        if (!$builder->document instanceof Document) {
            $builder->document = new Document();
            $key = basename($path);
            $builder->document->setKey($key);
            $parentPath = dirname($path);
            $parent = Document::getByPath($parentPath);
            $builder->document->setParentId($parent->getId());
            $builder->document->save(); // must be already saved for some actions
        }
        return $builder;
    }

    public function setPublished(bool $published): self
    {
        $this->document->setPublished($published);
        return $this;
    }

    /**
     * @throws DuplicateFullPathException
     */
    public function save(array $parameters = []): self
    {
        $this->document->save($parameters);
        return $this;
    }

    public function setIndex(int $index): self
    {
        $this->document->setIndex($index);
        return $this;
    }

    public function setType(string $type): self
    {
        $this->document->setType($type);
        return $this;
    }

    public function setProperty(
        string $name,
        string $type,
        mixed $data,
        bool $inherited = false,
        bool $inheritable = false
    ): self
    {
        $this->document->setProperty($name, $type, $data, $inherited, $inheritable);
        return $this;
    }

    public function getDocument(): Document
    {
        if (null === $this->document) {
            throw new \LogicException("Document object has not been set");
        }
        return $this->document;
    }
}
