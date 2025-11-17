<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Model\Document\Email;
use Pimcore\Model\Document\Hardlink;

class HardLinkBuilder extends DocumentBuilder
{
    protected static function getDocumentClass(): string
    {
        return Hardlink::class;
    }

    public function setSourceId(?int $sourceId): static
    {
        $this->getObject()->setSourceId($sourceId);
        return $this;
    }

    public function setPropertiesFromSource(bool $propertiesFromSource): static
    {
        $this->getObject()->setPropertiesFromSource($propertiesFromSource);
        return $this;
    }

    public function setChildrenFromSource(bool $childrenFromSource): static
    {
        $this->getObject()->setChildrenFromSource($childrenFromSource);
        return $this;
    }

    public function getObject(): Hardlink
    {
        if (!$this->document instanceof Hardlink) {
            throw new LogicException('Hardlink object has not been set');
        }
        return $this->document;
    }
}
