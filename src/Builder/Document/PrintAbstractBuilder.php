<?php

namespace PimcoreContentMigration\Builder\Document;

abstract class PrintAbstractBuilder extends PageSnippetBuilder
{
    public function setLastGeneratedDate(\DateTime $lastGenerated): static
    {
        $this->document->setLastGeneratedDate($lastGenerated);
        return $this;
    }

    public function setLastGenerated(int $lastGenerated): static
    {
        $this->document->setLastGenerated($lastGenerated);
        return $this;
    }

    public function setLastGenerateMessage(string $lastGenerateMessage): static
    {
        $this->document->setLastGenerateMessage($lastGenerateMessage);
        return $this;
    }
}
