<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Link;

class LinkBuilder extends DocumentBuilder
{
    protected static function getDocumentClass(): string
    {
        return Link::class;
    }

    public function setDirect(string $direct): self
    {
        $this->document->setDirect($direct);
        return $this;
    }

    public function setLinktype(string $linktype): self
    {
        $this->document->setLinktype($linktype);
        return $this;
    }

}
