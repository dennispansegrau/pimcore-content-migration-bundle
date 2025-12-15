<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Model\Document\Snippet;

class SnippetBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Snippet::class;
    }

    public function getObject(): Snippet
    {
        if (!$this->document instanceof Snippet) {
            throw new LogicException('Snippet object has not been set');
        }
        return $this->document;
    }
}
