<?php

namespace PimcoreContentMigration\Builder\Asset;

use LogicException;
use Pimcore\Model\Asset;

class ImageBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Image::class;
    }

    public function getObject(): Asset\Image
    {
        if (!$this->asset instanceof Asset\Image) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
