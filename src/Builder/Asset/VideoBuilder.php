<?php

namespace PimcoreContentMigration\Builder\Asset;

use Pimcore\Model\Asset;
use LogicException;

class VideoBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Video::class;
    }

    public function getObject(): Asset\Video
    {
        if (!$this->asset instanceof Asset\Video) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
