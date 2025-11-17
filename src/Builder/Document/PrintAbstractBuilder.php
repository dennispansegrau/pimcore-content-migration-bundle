<?php

namespace PimcoreContentMigration\Builder\Document;

use DateTime;
use LogicException;
use Pimcore\Bundle\WebToPrintBundle\Model\Document\PrintAbstract;

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

    public function getObject(): PrintAbstract
    {
        if (!$this->document instanceof PrintAbstract) {
            throw new LogicException('PrintAbstract object has not been set');
        }
        return $this->document;
    }
}
