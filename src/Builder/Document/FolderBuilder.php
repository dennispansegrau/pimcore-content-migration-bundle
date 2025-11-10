<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Folder;

class FolderBuilder extends DocumentBuilder
{
    protected static function getDocumentClass(): string
    {
        return Folder::class;
    }
}
