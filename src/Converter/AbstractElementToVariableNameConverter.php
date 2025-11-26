<?php

namespace PimcoreContentMigration\Converter;

use function array_map;
use function implode;
use function lcfirst;

use LogicException;
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
                return preg_replace('/[^A-Za-z0-9]/', '_', $part);
            },
            $segments
        );
        return lcfirst(implode('', $segments));
    }
}
