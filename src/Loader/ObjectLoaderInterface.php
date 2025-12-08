<?php

namespace PimcoreContentMigration\Loader;

use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Exception\NotFoundException;
use PimcoreContentMigration\MigrationType;

interface ObjectLoaderInterface
{
    /**
     * @throws NotFoundException
     */
    public function loadObject(MigrationType $type, int $id): AbstractElement;
}
