<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Bundle\WebToPrintBundle\Model\Document\Printcontainer;

class PrintContainerBuilder extends PrintAbstractBuilder
{
    protected static function getDocumentClass(): string
    {
        return Printcontainer::class;
    }
}
