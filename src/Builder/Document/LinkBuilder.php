<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Model\Document\Link;

class LinkBuilder extends DocumentBuilder
{
    protected static function getDocumentClass(): string
    {
        return Link::class;
    }

    public function setDirect(string $direct): static
    {
        $this->getObject()->setDirect($direct);
        return $this;
    }

    public function setLinktype(string $linktype): static
    {
        $this->getObject()->setLinktype($linktype);
        return $this;
    }

    public function getObject(): Link
    {
        if (!$this->document instanceof Link) {
            throw new LogicException('Link object has not been set');
        }
        return $this->document;
    }
}
