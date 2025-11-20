<?php

namespace PimcoreContentMigration\Builder\Asset;

use LogicException;
use Pimcore\Model\Asset;

class DocumentBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Document::class;
    }

    public function getObject(): Asset\Document
    {
        if (!$this->asset instanceof Asset\Document) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
