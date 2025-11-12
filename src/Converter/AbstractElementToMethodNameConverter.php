<?php

namespace PimcoreContentMigration\Converter;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;

class AbstractElementToMethodNameConverter
{
    private const PREFIX = 'createOrUpdate';

    public function convert(AbstractElement $abstractElement): string
    {
        $segments = preg_split('#[\\/]+#', trim($abstractElement->getFullPath(), '/'));
        $segments = array_map(
            function($part) {
                $part = ucwords($part, "-_ ");
                return preg_replace('/[^A-Za-z0-9]/', '_', $part);
            },
            $segments
        );

        $path = implode('', $segments);

        if ($abstractElement instanceof Document) {
            return self::PREFIX . 'Document' . $path;
        } elseif ($abstractElement instanceof Asset) {
            return self::PREFIX . 'Asset' . $path;
        } elseif ($abstractElement instanceof DataObject) {
            return self::PREFIX . 'Object' . $path;
        } else {
            throw new \LogicException('Unknown element type: ' . $abstractElement->getType());
        }
    }
}
