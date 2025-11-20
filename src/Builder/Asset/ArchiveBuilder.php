<?php

namespace PimcoreContentMigration\Builder\Asset;

use LogicException;
use Pimcore\Model\Asset;

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
