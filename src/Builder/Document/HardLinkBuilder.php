<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Hardlink;

class HardLinkBuilder extends DocumentBuilder
{
    protected static function getDocumentClass(): string
    {
        return Hardlink::class;
    }

    public function setSourceId(?int $sourceId): static
    {
        $this->document->setSourceId($sourceId);
        return $this;
    }

    public function setPropertiesFromSource(bool $propertiesFromSource): static
    {
        $this->document->setPropertiesFromSource($propertiesFromSource);
        return $this;
    }

    public function setChildrenFromSource(bool $childrenFromSource): static
    {
        $this->document->setChildrenFromSource($childrenFromSource);
        return $this;
    }
}
