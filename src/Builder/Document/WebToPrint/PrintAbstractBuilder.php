<?php

namespace PimcoreContentMigration\Builder\Document\WebToPrint;

use DateTime;
use LogicException;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;
use Pimcore\Model\Document\PageSnippet;
use PimcoreContentMigration\Builder\Document\PageSnippetBuilder;

abstract class PrintAbstractBuilder extends PageSnippetBuilder
{
    public function setLastGeneratedDate(DateTime $lastGenerated): static
    {
        $this->getObject()->setLastGeneratedDate($lastGenerated);
        return $this;
    }

    public function setLastGenerated(int $lastGenerated): static
    {
        $this->getObject()->setLastGenerated($lastGenerated);
        return $this;
    }

    public function setLastGenerateMessage(string $lastGenerateMessage): static
    {
        $this->getObject()->setLastGenerateMessage($lastGenerateMessage);
        return $this;
    }

    /**
     * @return PrintAbstract
     */
    public function getObject(): PageSnippet
    {
        if (!$this->document instanceof PrintAbstract) {
            throw new LogicException('PrintAbstract object has not been set');
        }
        return $this->document;
    }
}
