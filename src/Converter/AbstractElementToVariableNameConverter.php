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

class AbstractElementToVariableNameConverter
{
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
        $name = implode('', $segments);
        if (empty($name)) {
            $name = 'Root';
        }

        if ($abstractElement instanceof Document) {
            return 'document' . $name;
        } elseif ($abstractElement instanceof Asset) {
            return 'asset' . $name;
        } elseif ($abstractElement instanceof DataObject) {
            return 'object' . $name;
        } else {
            throw new LogicException('Unknown element type: ' . $abstractElement->getType());
        }
    }
}
