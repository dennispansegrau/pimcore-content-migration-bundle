<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Loader;

use Pimcore\Model\Element\AbstractElement;

interface ObjectLoaderInterface
{
    public function loadObject(string $type, int $id): AbstractElement;
}
