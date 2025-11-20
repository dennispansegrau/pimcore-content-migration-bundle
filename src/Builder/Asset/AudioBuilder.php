<?php

namespace PimcoreContentMigration\Builder\Asset;

use LogicException;
use Pimcore\Model\Asset;

class AudioBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Audio::class;
    }

    public function getObject(): Asset\Audio
    {
        if (!$this->asset instanceof Asset\Audio) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
