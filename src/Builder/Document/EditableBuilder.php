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

    public function createSelect(
        string $name,
        string $text,
    ): Editable\Select {
        $editable = new Editable\Select();
        $editable->setName($name);
        $editable->setDataFromResource($text);
        return $editable;
    }

    public function createCheckbox(
        string $name,
        bool $value,
    ): Editable\Checkbox {
        $editable = new Editable\Checkbox();
        $editable->setName($name);
        $editable->setDataFromResource($value);
        return $editable;
    }

    public function createDate(
        string $name,
        ?int $timestamp,
    ): Editable\Date {
        $editable = new Editable\Date();
        $editable->setName($name);
        $editable->setDataFromResource($timestamp);
        return $editable;
    }

    public function createNumeric(
        string $name,
        string $number,
    ): Editable\Numeric {
        $editable = new Editable\Numeric();
        $editable->setName($name);
        $editable->setDataFromResource($number);
        return $editable;
    }

    public function createPdf(
        string $name,
        int $assetId,
    ): Editable\Pdf {
        $editable = new Editable\Pdf();
        $editable->setName($name);
        $editable->setDataFromResource([
            'id' => $assetId,
        ]);
        return $editable;
    }

    public function createTable(
        string $name,
        array $data,
    ): Editable\Table {
        $editable = new Editable\Table();
        $editable->setName($name);
        $editable->setDataFromResource($data);
        return $editable;
    }

    public function createSnippet(
        string $name,
        int $snippedId,
    ): Editable\Snippet {
        $editable = new Editable\Snippet();
        $editable->setName($name);
        $editable->setId($snippedId);
        return $editable;
    }

    public function createVideo(
        string $name,
        int|string $videoId,
        string $type,
        string $title,
        string $description,
        ?int $posterAssetId,
    ): Editable\Video {
        $editable = new Editable\Video();
        $editable->setName($name);
        $editable->setDataFromResource([
            'id' => $videoId,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'poster' => $posterAssetId,
        ]);
        return $editable;
    }

    public function createRelation(
        string $name,
        int $id,
        string $type,
        string $subType,
    ): Editable\Relation {
        $editable = new Editable\Relation();
        $editable->setName($name);
        $editable->setDataFromResource([
            'id' => $id,
            'type' => $type,
            'subtype' => $subType,
        ]);
        return $editable;
    }

    /**
     * @param int[] $ids
     */
    public function createRelations(
        string $name,
        array $ids
    ): Editable\Relations {
        $editable = new Editable\Relations();
        $editable->setName($name);
        $editable->setDataFromEditmode($ids);
        return $editable;
    }

    public function createEmbed(
        string $name,
        string $url,
    ): Editable\Embed {
        $editable = new Editable\Embed();
        $editable->setName($name);
        $editable->setDataFromResource([
            'url' => $url,
        ]);
        return $editable;
    }

    /**
     * @param string[] $values
     */
    public function createMultiselect(
        string $name,
        array $values,
    ): Editable\Multiselect {
        $editable = new Editable\Multiselect();
        $editable->setName($name);
        $editable->setDataFromResource($values);
        return $editable;
    }

    public function createArea(
        string $name,
        string $type,
    ): Editable\Area {
        $editable = new Editable\Area();
        $editable->setName($name);
        $editable->setDataFromResource([
            'type' => $type,
        ]);
        return $editable;
    }

    /**
     * @param array<int, array<string, string|bool>> $blocks
     */
    public function createAreablock(
        string $name,
        array $blocks,
    ): Editable\Areablock {
        $editable = new Editable\Areablock();
        $editable->setName($name);
        $editable->setDataFromResource($blocks);
        return $editable;
    }

    public function createRenderlet(
        string $name,
        int $id,
        string $type,
        string $subType,
    ): Editable\Renderlet {
        $editable = new Editable\Renderlet();
        $editable->setName($name);
        $editable->setDataFromResource([
            'id' => $id,
            'type' => $type,
            'subtype' => $subType,
        ]);
        return $editable;
    }

    /**
     * @param array<string, string> $data
     */
    public function createBlock(
        string $name,
        array $data,
    ): Editable\Block {
        $editable = new Editable\Block();
        $editable->setName($name);
        $editable->setDataFromResource($data);
        return $editable;
    }

    /**
     * @param array<string, string> $data
     */
    public function createScheduledBlock(
        string $name,
        array $data,
    ): Editable\Scheduledblock {
        $editable = new Editable\Scheduledblock();
        $editable->setName($name);
        $editable->setDataFromResource($data);
        return $editable;
    }

    public function createLink(
        string $name,
        ?string $internalType,
        string $linkType,
        string $text,
        string $path,
        string $target,
        string $parameters,
        string $anchor,
        string $title,
        string $accesskey,
        string $rel,
        string $tabindex,
        string $class,
        string $attributes,
        bool $internal,
        ?int $internalId,
    ): Editable\Link {
        $editable = new Editable\Link();
        $editable->setName($name);
        $editable->setDataFromResource([
            'internalType' => $internalType,
            'linktype' => $linkType,
            'text' => $text,
            'path' => $path,
            'target' => $target,
            'parameters' => $parameters,
            'anchor' => $anchor,
            'title' => $title,
            'accesskey' => $accesskey,
            'rel' => $rel,
            'tabindex' => $tabindex,
            'class' => $class,
            'attributes' => $attributes,
            'internal' => $internal,
            'internalId' => $internalId,
        ]);
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
