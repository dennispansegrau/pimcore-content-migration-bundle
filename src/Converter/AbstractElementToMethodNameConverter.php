<?php

namespace PimcoreContentMigration\Converter;

use function array_map;
use function implode;

use LogicException;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Element\AbstractElement;

use function preg_replace;
use function preg_split;
use function trim;
use function ucwords;

class AbstractElementToMethodNameConverter
{
    private const PREFIX = 'findOrCreate';

    public function convert(AbstractElement $abstractElement): string
    {
        $segments = preg_split('#[/]+#', trim($abstractElement->getFullPath(), '/'));
        if ($segments === false) {
            throw new LogicException('Failed to split path: ' . $abstractElement->getFullPath());
        }
        $segments = array_map(
            function ($part) {
                $part = ucwords($part, '-_ ');
                $part = preg_replace('/[^A-Za-z0-9]/', '_', $part);
                return preg_replace('/(?<!_)_(?!_)/', '', $part ?? '');
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
            throw new LogicException('Unknown element type: ' . $abstractElement->getType());
        }
    }
}
