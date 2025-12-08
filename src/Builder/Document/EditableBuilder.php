<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Element\Data\MarkerHotspotItem;

class EditableBuilder
{
    /**
     * @param array<int, array<string, mixed>> $hotspots
     * @param array<int, array<string, mixed>> $marker
     */
    public function createImage(
        string $name,
        Asset\Image $image,
        string $alt,
        bool $cropPercent,
        float $cropWidth,
        float $cropHeight,
        float $cropTop,
        float $cropLeft,
        array $hotspots,
        array $marker
    ): Editable\Image {
        $editable = new Editable\Image();
        $editable->setName($name);
        $editable->setText($alt);
        $editable->setImage($image);
        $editable->setCropPercent($cropPercent);
        $editable->setCropWidth($cropWidth);
        $editable->setCropHeight($cropHeight);
        $editable->setCropTop($cropTop);
        $editable->setCropLeft($cropLeft);
        $editable->setHotspots($hotspots);
        $editable->setMarker($marker);
        return $editable;
    }

    public function createInput(
        string $name,
        string $text,
    ): Editable\Input {
        $editable = new Editable\Input();
        $editable->setName($name);
        $editable->setDataFromResource($text);
        return $editable;
    }

    public function createTextarea(
        string $name,
        string $text,
    ): Editable\Textarea {
        $editable = new Editable\Textarea();
        $editable->setName($name);
        $editable->setDataFromResource($text);
        return $editable;
    }

    public function createLink(
        string $name,
        string $text,
    ): Editable\Link {
        //        'internalType' => 'document',
        //        'linktype' => 'internal',
        //        'text' => 'Finde deinen Oldtimer',
        //        'path' => '/de/Finden-und-Kaufen',
        //        'target' => '',
        //        'parameters' => '',
        //        'anchor' => '',
        //        'title' => '',
        //        'accesskey' => '',
        //        'rel' => '',
        //        'tabindex' => '',
        //        'class' => '',
        //        'attributes' => '',
        //        'internal' => true,
        //        'internalId' => 138,
        $editable = new Editable\Link();
        $editable->setName($name);
        $editable->setDataFromResource($text);
        return $editable;
    }

    /**
     * @param MarkerHotspotItem[] $markerHotspotItems
     * @return array<string, mixed>
     */
    public function createHotspot(float $top, float $left, float $width, float $height, array $markerHotspotItems, ?string $name): array
    {
        return [
            'top' => $top,
            'left' => $left,
            'width' => $width,
            'height' => $height,
            'data' => $markerHotspotItems,
            'name' => $name,
        ];
    }

    /**
     * @param MarkerHotspotItem[] $markerHotspotItems
     * @return array<string, mixed>
     */
    public function createMarker(float $top, float $left, array $markerHotspotItems, ?string $name): array
    {
        return [
            'top' => $top,
            'left' => $left,
            'data' => $markerHotspotItems,
            'name' => $name,
        ];
    }

    /**
     * If the type is document, asset, or object and the value is a string (path),
     * the path is automatically resolved and converted into a valid ID.
     */
    public function createMarkerHotspotItem(string $name, string $type, null|string|bool|int $value): MarkerHotspotItem
    {
        return new MarkerHotspotItem([
            'name' => $name,
            'type' => $type,
            'value' => $value,
        ]);
    }
}
