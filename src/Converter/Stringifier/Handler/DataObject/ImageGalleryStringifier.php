<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\ImageGallery;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class ImageGalleryStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof ImageGallery;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var ImageGallery $value */
        $items = $value->getItems();

        return sprintf(
            'new \Pimcore\Model\DataObject\Data\ImageGallery(%s)',
            empty($items) ?
                '[]' :
                $this->getConverter()->convertValueToString($items, $dependencyList, $parameters)
        );
    }
}
