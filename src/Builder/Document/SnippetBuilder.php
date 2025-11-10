<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Snippet;

class SnippetBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Snippet::class;
    }
}
