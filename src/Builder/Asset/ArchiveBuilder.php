<?php

namespace PimcoreContentMigration\Builder\Asset;

use Pimcore\Model\Asset;
use LogicException;

class ArchiveBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Archive::class;
    }

    public function getObject(): Asset\Archive
    {
        if (!$this->asset instanceof Asset\Archive) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
