<?php

namespace PimcoreContentMigration\Builder;

use Pimcore\Model\Document;
use Pimcore\Model\Element\DuplicateFullPathException;

class DocumentBuilder
{
    private Document $document;

    private function __construct()
    {
    }

    /**
     * @throws \Exception
     */
    public static function openOrCreateDocument(string $path): self
    {
        $builder = new self();
        $builder->document = Document::getByPath($path);
        if (!$builder->document instanceof Document) {
            $builder->document = new Document();
            $key = basename($path);
            $builder->document->setKey($key);
            $parentPath = dirname($path);
            $parent = Document::getByPath($parentPath);
            $builder->document->setParentId($parent);
        }
        return $builder;
    }

    public function setPublished(bool $isPublished): self
    {
        $this->document->setPublished($isPublished);
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
}
