<?php

namespace PimcoreContentMigration\Twig\Extension;

use Carbon\Carbon;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Data\Consent;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use PimcoreContentMigration\Builder\DataObject\FieldcollectionBuilder;
use PimcoreContentMigration\Builder\DataObject\FieldcollectionItemBuilder;
use PimcoreContentMigration\Builder\DataObject\LocalizedfieldBuilder;
use PimcoreContentMigration\Builder\DataObject\ObjectbrickBuilder;
use PimcoreContentMigration\Builder\DataObject\ObjectbrickItemBuilder;
use function array_keys;
use function get_class;
use function gettype;
use function in_array;

use InvalidArgumentException;

use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;

use LogicException;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\Document\Editable\Link;
use Pimcore\Model\Document\Editable\Pdf;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Editable\Renderlet;
use Pimcore\Model\Document\Editable\Snippet;
use Pimcore\Model\Document\Editable\Video;
use Pimcore\Model\Document\Editable\Wysiwyg;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;
use RuntimeException;

use function sprintf;
use function str_repeat;
use function str_replace;
use function str_starts_with;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ValueToStringExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pcmb_value_to_string', [$this, 'valueToString']),
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     * Converts any value into a readable string.
     */
    public function valueToString(mixed $value, DependencyList $dependencyList, array $parameters = []): string
    {
        if ($value instanceof MarkerHotspotItem) {
            return $this->handleMarkerHotspotItem($value, $dependencyList);
        }

        if ($value instanceof Link) {
            return $this->handleLink($value, $dependencyList);
        }

        if ($value instanceof Renderlet || $value instanceof Relation) {
            return $this->handleRelation($value, $dependencyList);
        }

        if ($value instanceof Pdf) {
            return $this->handlePdf($value, $dependencyList);
        }

        if ($value instanceof Snippet) {
            return $this->handleSnippet($value, $dependencyList);
        }

        if ($value instanceof Wysiwyg) {
            return $this->handleWysiwyg($value, $dependencyList, $parameters);
        }

        if ($value instanceof Video) {
            return $this->handleVideo($value, $dependencyList, $parameters);
        }

        if ($value instanceof ImageGallery) {
            return $this->handleImageGallery($value, $dependencyList, $parameters);
        }

        if ($value instanceof Hotspotimage) {
            return $this->handleHotspotimage($value, $dependencyList, $parameters);
        }

        if ($value instanceof UrlSlug) {
            return $this->handleUrlSlug($value, $dependencyList, $parameters);
        }

        if ($value instanceof GeoCoordinates) {
            return $this->handleGeoCoordinates($value, $dependencyList, $parameters);
        }

        if ($value instanceof Objectbrick) {
            return $this->handleObjectbrick($value, $dependencyList, $parameters);
        }

        if ($value instanceof Objectbrick\Data\AbstractData) {
            return $this->handleObjectbrickItem($value, $dependencyList, $parameters);
        }

        if ($value instanceof QuantityValue) {
            return $this->handleQuantityValue($value, $dependencyList, $parameters);
        }

        if ($value instanceof Consent) {
            return $this->handleConsent($value, $dependencyList, $parameters);
        }

        if (is_string($value) && str_starts_with($value, 'new ')) { // special case for new *Class*
            return $value;
        }

        if ($value instanceof Carbon) {
            return $this->handleCarbon($value, $dependencyList, $parameters);
        }

        if ($value instanceof \Pimcore\Model\DataObject\Data\Video) {
            return $this->handleDataVideo($value, $dependencyList, $parameters);
        }

        if ($value instanceof Fieldcollection) {
            return $this->handleFieldcollection($value, $dependencyList, $parameters);
        }

        if ($value instanceof AbstractData) {
            return $this->handleFieldcollectionItem($value, $dependencyList, $parameters);
        }

        if ($value instanceof Localizedfield) {
            return $this->handleLocalizedfield($value, $dependencyList, $parameters);
        }

        if ($value instanceof BlockElement) {
            return $this->handleBlockElement($value, $dependencyList, $parameters);
        }

        if ($value instanceof \Pimcore\Model\DataObject\Data\Link) {
            return $this->handleDataObjectLink($value, $dependencyList, $parameters);
        }

        return $this->renderScalarOrComplex($value, $dependencyList, $parameters);
    }

    private function idToDependencyString(
        string $type,
        int $id,
        DependencyList $dependencyList,
        bool $fallbackToId = true,
        bool $forceObject = false
    ): string {
        $dependency = $dependencyList->getByTypeAndId($type, $id);

        if ($dependency === null) {
            return $fallbackToId ? (string) $id : 'null';
        }

        if ($forceObject) {
            return '$' . $dependency->getVariableName();
        }

        return '(int) $' . $dependency->getVariableName() . '->getId()';
    }

    private function getIndent(array $parameters, int $default = 12): int
    {
        return is_numeric($parameters['indent'] ?? null)
            ? (int) $parameters['indent']
            : $default;
    }

    private function handleMarkerHotspotItem(
        MarkerHotspotItem $item,
        DependencyList $dependencyList
    ): string {
        if (!in_array($item->getType(), ['document', 'asset', 'object'], true)) {
            return $this->valueToString($item->getValue(), $dependencyList);
        }

        $id = $item->getValue();

        if (!is_int($id)) {
            throw new LogicException('Invalid value type in MarkerHotspotItem.');
        }

        return $this->idToDependencyString($item->getType(), $id, $dependencyList, false);
    }

    private function handleRelation(
        Renderlet|Relation $value,
        DependencyList $dependencyList
    ): string {
        $data = $value->getData();

        if (!is_array($data) || !isset($data['type'], $data['id'])) {
            throw new LogicException('Invalid data.');
        }

        if (!is_string($data['type']) || !is_int($data['id'])) {
            throw new LogicException('Invalid data.');
        }

        return $this->idToDependencyString(
            $data['type'],
            $data['id'],
            $dependencyList
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleWysiwyg(
        Wysiwyg $value,
        DependencyList $dependencyList,
        array $parameters
    ): string {
        $dependencies = $value->resolveDependencies();
        if (empty($dependencies)) {
            return '[]';
        }

        $indent = $this->getIndent($parameters);
        $result = "[\n";

        foreach ($dependencies as $data) {
            if (!is_array($data) || !isset($data['type'], $data['id'])) {
                continue;
            }

            $replacement = $this->idToDependencyString(
                $data['type'],
                $data['id'],
                $dependencyList
            );

            $result .= str_repeat(' ', $indent + 4)
                . "'{$data['type']}' => [\n"
                . str_repeat(' ', $indent + 8)
                . "{$data['id']} => {$replacement},\n"
                . str_repeat(' ', $indent + 4)
                . "],\n";
        }

        return $result . str_repeat(' ', $indent) . ']';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function renderScalarOrComplex(
        mixed $value,
        DependencyList $dependencyList,
        array $parameters
    ): string {
        return match (true) {
            $value === null => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            is_int($value), is_float($value) => (string) $value,
            is_string($value) => '\'' . str_replace('\'', '\\\'', $value) . '\'',
            is_array($value) => $this->renderArray($value, $dependencyList, $parameters),
            $value instanceof AbstractElement => $this->renderAbstractElement($value, $dependencyList),
            is_object($value) => throw new InvalidArgumentException('Unsupported object of class: ' . get_class($value)),
            default => throw new InvalidArgumentException('Unsupported value type: ' . gettype($value)),
        };
    }

    private function handleLink(Link $value, DependencyList $dependencyList): string {
        $data = $value->getData();

        if (!is_array($data)) {
            return 'null';
        }

        $type = $data['internalType'] ?? null;
        $id = $data['internalId'] ?? null;

        if (!is_string($type) || !is_int($id)) {
            return 'null';
        }

        return $this->idToDependencyString($type, $id, $dependencyList, false);
    }

    private function handlePdf(
        Pdf $value,
        DependencyList $dependencyList
    ): string {
        $asset = $value->getElement();

        if ($asset === null) {
            return (string) $value->getId();
        }

        return $this->idToDependencyString(
            'asset',
            $asset->getId(),
            $dependencyList
        );
    }

    private function handleSnippet(
        Snippet $value,
        DependencyList $dependencyList
    ): string {
        $snippet = $value->getSnippet();

        if ($snippet === null) {
            return (string) $value->getId();
        }

        return $this->idToDependencyString(
            'document',
            $value->getId(),
            $dependencyList
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleVideo(
        Video $value,
        DependencyList $dependencyList,
        array $parameters
    ): string {
        if (!isset($parameters['field'])) {
            throw new InvalidArgumentException(
                'Editable type video needs field parameter with value id or poster.'
            );
        }

        $field = $parameters['field'];

        $id = match ($field) {
            'id' => $value->getId(),
            'poster' => $value->getPoster(),
            default => throw new InvalidArgumentException(
                'Editable type video needs field parameter with value id or poster.'
            ),
        };

        if (!is_int($id)) {
            return $this->renderScalarOrComplex($id, $dependencyList, $parameters);
        }

        return $this->idToDependencyString(
            'asset',
            $id,
            $dependencyList
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function renderArray(
        array $value,
        DependencyList $dependencyList,
        array $parameters
    ): string {
        if ($value === []) {
            return '[]';
        }

        $indent = $this->getIndent($parameters);
        $result = "[\n";

        foreach ($value as $key => $item) {
            $result .= str_repeat(' ', $indent + 4);
            if (is_int($key)) {
                $result .= $key . ' => ';
            } else {
                $result .= "'" . $key . "' => ";
            }
            $result .= $this->valueToString(
                $item,
                $dependencyList,
                ['indent' => $indent + 4]
            );
            $result .= ",\n";
        }

        return $result . str_repeat(' ', $indent) . ']';
    }

    private function renderAbstractElement(
        AbstractElement $value,
        DependencyList $dependencyList
    ): string {
        $dependency = $dependencyList->getDependency($value);

        if (!$dependency instanceof Dependency) {
            return '\\' . get_class($value)
                . "::getByPath('"
                . $value->getFullPath()
                . "')";
        }

        return '$' . $dependency->getVariableName();
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleImageGallery(ImageGallery $imageGallery, DependencyList $dependencyList, array $parameters): string
    {
        $items = $imageGallery->getItems();

        return sprintf(
            'new \Pimcore\Model\DataObject\Data\ImageGallery(%s)',
            empty($items) ?
                '[]' :
                $this->renderArray($items, $dependencyList, $parameters)
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleHotspotimage(Hotspotimage $hotspotimage, DependencyList $dependencyList, array $parameters): string
    {
        $image = $hotspotimage->getImage();
        $hotspot = $hotspotimage->getHotspots();
        $marker = $hotspotimage->getMarker();
        $crop = $hotspotimage->getCrop();

        $imageString = empty($image) ? 'null' : $this->renderAbstractElement($image, $dependencyList);
        $hotspotString = empty($image) ? 'null' : $this->valueToString($hotspot, $dependencyList, $parameters);
        $markerString = empty($image) ? 'null' : $this->valueToString($marker, $dependencyList, $parameters);
        $cropString = empty($image) ? 'null' : $this->valueToString($crop, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\Hotspotimage(%s, %s, %s, %s)', $imageString, $hotspotString, $markerString, $cropString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleUrlSlug(UrlSlug $urlSlug, DependencyList $dependencyList, array $parameters): string
    {
        $slug = $urlSlug->getSlug();
        $siteId = $urlSlug->getSiteId();

        $slugString = $this->valueToString($slug, $dependencyList, $parameters);
        $siteIdString = $this->valueToString($siteId, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\UrlSlug(%s, %s)', $slugString, $siteIdString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleGeoCoordinates(GeoCoordinates $geoCoordinates, DependencyList $dependencyList, array $parameters): string
    {
        $lat = $geoCoordinates->getLatitude();
        $long = $geoCoordinates->getLongitude();

        $latString = $this->valueToString($lat, $dependencyList, $parameters);
        $longString = $this->valueToString($long, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\GeoCoordinates(%s, %s)', $latString, $longString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleQuantityValue(QuantityValue $quantityValue, DependencyList $dependencyList, array $parameters): string
    {
        $value = $quantityValue->getValue();
        $unitId = $quantityValue->getUnit()?->getId();
        if ($unitId === null) {
            throw new InvalidArgumentException('QuantityValue must have a unit with an id.');
        }

        $valueString = $this->valueToString($value, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\QuantityValue(%s, \'%s\')', $valueString, $unitId);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleConsent(Consent $consent, DependencyList $dependencyList, array $parameters): string
    {
        $noteId = $consent->getNoteId();
        if ($noteId === null) {
            $noteId = 'null';
        }
        return sprintf('new \Pimcore\Model\DataObject\Data\Consent(%s, %s)',
            $consent->getConsent() ? 'true' : 'false',
            (string) $noteId
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleCarbon(Carbon $carbon, DependencyList $dependencyList, array $parameters): string
    {
        $time = $carbon->toDateTimeString();
        $timezone = $carbon->getTimezone()->getName();
        return sprintf('new \Carbon\Carbon(\'%s\', \'%s\')', $time, $timezone);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleDataVideo(\Pimcore\Model\DataObject\Data\Video $video, DependencyList $dependencyList, array $parameters): string
    {
        $data = [
            'type' => $video->getType() ?? '',
            'data' => $video->getData(),
            'poster' => $video->getPoster(),
            'title' => $video->getTitle() ?? '',
            'description' => $video->getDescription() ?? '',
        ];
        return $this->valueToString($data, $dependencyList, $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleLocalizedfield(Localizedfield $value, DependencyList $dependencyList, array $parameters): string
    {
        $builderName = LocalizedfieldBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        $values = $this->valueToString($value->getItems(), $dependencyList, $parameters);
        return sprintf('\%s::create(%s)->setLocalizedValues(%s)->getObject()', $builderName, $owner, $values);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleFieldcollection(Fieldcollection $fieldCollection, DependencyList $dependencyList, array $parameters): string
    {
        $builderName = FieldcollectionBuilder::class;
        $fields = $this->valueToString($fieldCollection->getItems(), $dependencyList, $parameters);
        return sprintf('\%s::create(\'%s\', %s)->getObject()', $builderName, $fieldCollection->getFieldname(), $fields);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleFieldcollectionItem(AbstractData $element, DependencyList $dependencyList, array $parameters): string
    {
        $fields = array_keys($element->getDefinition()->getFieldDefinitions());
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $element->get($field);
        }

        $setter = [];
        foreach ($values as $field => $value) {
            $setter[] = sprintf('->set(\'%s\', %s)', $field, $this->valueToString($value, $dependencyList, $parameters));
        }

        $setterString = '';
        if (!empty($setter)) {
            $setterString = "\n" . implode("\n", $setter) . "\n";
        }

        $builderName = FieldcollectionItemBuilder::class;
        return sprintf('\%s::create(\%s::class)%s->getObject()', $builderName, $element::class, $setterString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleObjectbrick(Objectbrick $objectbrick, DependencyList $dependencyList, array $parameters): string
    {
        $builderName = ObjectbrickBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        $setterString = '';
        $items = $objectbrick->getItems();
        if (!empty($items)) {
            $setterString = "->setItems([\n";
            foreach ($items as $item) {
                $setterString .= $this->renderArray($item, $dependencyList, $parameters) . ",\n";
            }
            $setterString .= "\n])";
        }
        return sprintf('\%s::create(\'%s\', %s)%s->getObject()', $builderName, $objectbrick->getFieldname(), $owner, $setterString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleObjectbrickItem(Objectbrick\Data\AbstractData $abstractData, DependencyList $dependencyList, array $parameters): string
    {
        $fields = array_keys($abstractData->getDefinition()->getFieldDefinitions());
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $abstractData->get($field);
        }

        $setter = [];
        foreach ($values as $field => $value) {
            $setter[] = sprintf('->set(\'%s\', %s)', $field, $this->valueToString($value, $dependencyList, $parameters));
        }

        $setterString = '';
        if (!empty($setter)) {
            $setterString = "\n" . implode("\n", $setter) . "\n";
        }

        $builderName = ObjectbrickItemBuilder::class;
        $owner = '$builder->getObject()';
        if (array_key_exists('owner', $parameters) &&
            is_string($parameters['owner'])) {
            $owner = $parameters['owner'];
        }
        return sprintf('\%s::create(\%s::class, %s)%s->getObject()', $builderName, $abstractData::class, $owner, $setterString);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleBlockElement(BlockElement $blockElement, DependencyList $dependencyList, array $parameters): string
    {
        $name = $blockElement->getName();
        $type = $blockElement->getType();
        $data = $blockElement->getData();
        $dataString = $this->valueToString($data, $dependencyList, $parameters);

        return sprintf('new \Pimcore\Model\DataObject\Data\BlockElement(\'%s\', \'%s\', %s)',
            $name,
            $type,
            $dataString
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function handleDataObjectLink(\Pimcore\Model\DataObject\Data\Link $link, DependencyList $dependencyList, array $parameters): string
    {
        $valuesString = $this->renderArray([
            'text' => $link->getText(),
            'internalType' => $link->getInternalType(),
            'internal' => $link->getInternal(),
            'direct' => $link->getDirect(),
            'linktype' => $link->getLinkType(),
            'target' => $link->getTarget(),
            'parameters' => $link->getParameters(),
            'anchor' => $link->getAnchor(),
            'title' => $link->getTitle(),
            'accesskey' => $link->getAccesskey(),
            'rel' => $link->getRel(),
            'tabindex' => $link->getTabindex(),
            'class' => $link->getClass(),
            'attributes' => $link->getAttributes(),
        ], $dependencyList, $parameters);

        $element = '';
        $linktype = $link->getLinktype();
        $internalType = $link->getInternalType();
        $id = $link->getInternal();
        if ($linktype === 'internal' && !empty($internalType) && !empty($id)) {
            if ($dependency = $this->idToDependencyString($internalType, $id, $dependencyList, false, true)) {
                $element = '->setElement(' . $dependency . ')';
            }
        }

        return sprintf('(new \Pimcore\Model\DataObject\Data\Link())->setValues(%s)%s', $valuesString, $element);
    }
}
