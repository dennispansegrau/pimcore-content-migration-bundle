<?php

namespace PimcoreContentMigration\Loader;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\MigrationType;

interface ObjectLoaderInterface
{
    public function loadObject(MigrationType $type, int $id): AbstractElement;
}
