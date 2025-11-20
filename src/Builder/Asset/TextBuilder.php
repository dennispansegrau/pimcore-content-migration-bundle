<?php

namespace PimcoreContentMigration\Builder\Asset;

use Pimcore\Model\Asset;
use LogicException;

class TextBuilder extends AssetBuilder
{
    protected static function getAssetClass(): string
    {
        return Asset\Text::class;
    }

    public function getObject(): Asset\Text
    {
        if (!$this->asset instanceof Asset\Text) {
            throw new LogicException('Asset object has not been set');
        }
        return $this->asset;
    }
}
