<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Loader;

use DennisPansegrau\PimcoreContentMigrationBundle\MigrationType;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Exception\NotFoundException;

class ObjectLoader implements ObjectLoaderInterface
{
    public function loadObject(string $type, int $id): AbstractElement
    {
        $object = null;
        if ($type === MigrationType::DOCUMENT->value) {
            $object = Document::getById($id);
        }
        if ($type === MigrationType::ASSET->value) {
            $object = Asset::getById($id);
        }
        if ($type === MigrationType::OBJECT->value) {
            $object = DataObject::getById($id);
        }
        if ($object === null) {
            throw new NotFoundException("Object of type {$type} with id {$id} not found");
        }
        return $object;
    }
}
