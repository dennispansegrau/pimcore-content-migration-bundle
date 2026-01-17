<?php

namespace PimcoreContentMigration\Converter\Stringifier\Handler\DataObject;

use Pimcore\Model\DataObject\Data\Hotspotimage;
use PimcoreContentMigration\Converter\Stringifier\Handler\Trait\ValueToStringConverterTrait;
use PimcoreContentMigration\Converter\Stringifier\ValueStringifier;
use PimcoreContentMigration\Generator\Dependency\DependencyList;

use function sprintf;

final class HotspotimageStringifier implements ValueStringifier
{
    use ValueToStringConverterTrait;

    public function supports(mixed $value, array $parameters = []): bool
    {
        return $value instanceof Hotspotimage;
    }

    public function toString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        /** @var Hotspotimage $value */
        $image = $value->getImage();
        $hotspot = $value->getHotspots();
        $marker = $value->getMarker();
        $crop = $value->getCrop();

        $imageString = empty($image) ? 'null' : $this->getConverter()->convertValueToString($image, $dependencyList);
        $hotspotString = empty($image) ? 'null' : $this->getConverter()->convertValueToString($hotspot, $dependencyList, $parameters);
        $markerString = empty($image) ? 'null' : $this->getConverter()->convertValueToString($marker, $dependencyList, $parameters);
        $cropString = empty($image) ? 'null' : $this->getConverter()->convertValueToString($crop, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\Hotspotimage(%s, %s, %s, %s)', $imageString, $hotspotString, $markerString, $cropString);
    }
}
