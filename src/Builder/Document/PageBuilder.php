<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Model\Document\Page;

class PageBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Page::class;
    }

    public function setDescription(string $description): static
    {
        $this->getObject()->setDescription($description);
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->getObject()->setTitle($title);
        return $this;
    }

    public function setPrettyUrl(?string $prettyUrl): static
    {
        $this->getObject()->setPrettyUrl($prettyUrl);
        return $this;
    }

    public function getObject(): Page
    {
        if (!$this->document instanceof Page) {
            throw new LogicException('Page object has not been set');
        }
        return $this->document;
    }
}
