<?php

namespace PimcoreContentMigration\Builder\Document\Personalization;

use LogicException;
use Pimcore\Bundle\PersonalizationBundle\Model\Document\Snippet;
use PimcoreContentMigration\Builder\Document\SnippetBuilder as DocumentSnippetBuilder;

class SnippetBuilder extends DocumentSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Snippet::class;
    }

    /**
     * @return Snippet
     */
    public function getObject(): \Pimcore\Model\Document\Snippet
    {
        if (!$this->document instanceof Snippet) {
            throw new LogicException('Snippet object has not been set');
        }
        return $this->document;
    }

    public function setUseTargetGroup(?int $useTargetGroup = null): static
    {
        $this->getObject()->setUseTargetGroup($useTargetGroup);
        return $this;
    }
}
