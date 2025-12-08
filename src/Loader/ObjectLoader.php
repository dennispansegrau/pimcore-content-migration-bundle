<?php

namespace PimcoreContentMigration\Loader;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Exception\NotFoundException;
use PimcoreContentMigration\MigrationType;

class ObjectLoader implements ObjectLoaderInterface
{
    /**
     * @throws NotFoundException
     */
    public function loadObject(MigrationType $type, int $id): AbstractElement
    {
        $object = null;
        if ($type === MigrationType::DOCUMENT) {
            $object = Document::getById($id);
        }
        if ($type === MigrationType::ASSET) {
            $object = Asset::getById($id);
        }
        if ($type === MigrationType::OBJECT) {
            $object = DataObject::getById($id);
        }
        if ($object === null) {
            throw new NotFoundException("Object of type {$type->value} with id {$id} not found");
        }
        return $object;
    }
}
