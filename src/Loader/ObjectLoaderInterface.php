<?php

namespace PimcoreContentMigration\Loader;

use Pimcore\Model\Element\AbstractElement;

interface ObjectLoaderInterface
{
    public function loadObject(string $type, int $id): AbstractElement;
}
