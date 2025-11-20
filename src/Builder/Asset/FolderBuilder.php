<?php

namespace PimcoreContentMigration\Builder\Asset;

use Pimcore\Model\Asset;
use LogicException;

class FolderBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Folder::class;
    }

    public function getObject(): Asset\Folder
    {
        if (!$this->asset instanceof Asset\Folder) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
