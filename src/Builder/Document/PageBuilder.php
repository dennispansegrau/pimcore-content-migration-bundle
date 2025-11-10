<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Page;

class PageBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Page::class;
    }

    public function setDescription(string $description): static
    {
        $this->document->setDescription($description);
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->document->setTitle($title);
        return $this;
    }

    public function setPrettyUrl(?string $prettyUrl): static
    {
        $this->document->setPrettyUrl($prettyUrl);
        return $this;
    }
}
