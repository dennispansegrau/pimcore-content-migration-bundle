<?php

namespace PimcoreContentMigration\Builder\Document\Personalization;

use LogicException;
use Pimcore\Bundle\PersonalizationBundle\Model\Document\Page;

class PageBuilder extends \PimcoreContentMigration\Builder\Document\PageBuilder
{
    protected static function getDocumentClass(): string
    {
        return Page::class;
    }

    public function getObject(): Page
    {
        if (!$this->document instanceof Page) {
            throw new LogicException('Page object has not been set');
        }
        return $this->document;
    }

    /**
     * @param int[]|string $targetGroupIds
     */
    public function setTargetGroupIds(array|string $targetGroupIds): static
    {
        $this->getObject()->setTargetGroupIds($targetGroupIds);
        return $this;
    }

    public function setUseTargetGroup(?int $useTargetGroup = null): static
    {
        $this->getObject()->setUseTargetGroup($useTargetGroup);
        return $this;
    }
}
