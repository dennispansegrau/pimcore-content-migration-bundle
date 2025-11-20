<?php

namespace PimcoreContentMigration\Builder\Asset;

use LogicException;
use Pimcore\Model\Asset;

class UnknownBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Unknown::class;
    }

    public function getObject(): Asset\Unknown
    {
        if (!$this->asset instanceof Asset\Unknown) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
