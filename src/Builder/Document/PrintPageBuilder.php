<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Bundle\WebToPrintBundle\Model\Document\Printpage;

class PrintPageBuilder extends PrintAbstractBuilder
{
    protected static function getDocumentClass(): string
    {
        return Printpage::class;
    }
}
