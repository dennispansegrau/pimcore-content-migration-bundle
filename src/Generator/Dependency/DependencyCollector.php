<?php

namespace PimcoreContentMigration\Generator\Dependency;

use function in_array;
use function is_array;
use function is_int;
use function is_iterable;
use function is_string;

use LogicException;
use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use Pimcore\Model\Exception\NotFoundException;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Converter\AbstractElementToVariableNameConverter;
use PimcoreContentMigration\Factory\CodeGeneratorFactoryInterface;
use PimcoreContentMigration\Generator\Settings;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\MigrationType;
use RuntimeException;

class DependencyCollector
{
    private ?CodeGeneratorFactoryInterface $codeGeneratorFactory = null;

    public function __construct(
        protected readonly AbstractElementToMethodNameConverter $methodNameConverter,
        protected readonly AbstractElementToVariableNameConverter $variableNameConverter,
        protected readonly ObjectLoaderInterface $objectLoader,
    ) {
    }

    public function setCodeGeneratorFactory(CodeGeneratorFactoryInterface $codeGeneratorFactory): void
    {
        $this->codeGeneratorFactory = $codeGeneratorFactory;
    }

    protected function getCodeGeneratorFactory(): CodeGeneratorFactoryInterface
    {
        if (null === $this->codeGeneratorFactory) {
            throw new RuntimeException('CodeGeneratorFactory not set');
        }
        return $this->codeGeneratorFactory;
    }

    /**
     * @param Settings $settings
     * @param AbstractElement $abstractElement
     * @param string[] $existingMethodNames
     * @return DependencyList
     */
    public function getDependencies(Settings $settings, AbstractElement $abstractElement, array &$existingMethodNames): DependencyList
    {
        $dependencies = [];

        if (!$settings->withDependencies()) {
            return new DependencyList($dependencies);
        }

        $this->collectDirectDependencies($abstractElement, $settings, $existingMethodNames, $dependencies);
        $this->collectMarkerDependencies($abstractElement, $settings, $existingMethodNames, $dependencies);

        return new DependencyList($dependencies);
    }

    /**
     * @param string[] $existingMethodNames
     * @param Dependency[] $dependencies
     */
    private function collectDirectDependencies(
        AbstractElement $element,
        Settings $settings,
        array &$existingMethodNames,
        array &$dependencies
    ): void {
        $requires = $element->getDependencies()->getRequires();
        if (empty($requires)) {
            return;
        }

        foreach ($requires as $dependencyData) {
            if (!is_array($dependencyData)) {
                throw new LogicException('Invalid dependency data type (array expected)');
            }
            $type = $dependencyData['type'] ?? null;
            $id = $dependencyData['id'] ?? null;

            if (!is_string($type) || !is_int($id)) {
                throw new LogicException('Invalid dependency data (string type and integer id expected)');
            }

            $dependencies[] = $this->createDependency(MigrationType::fromString($type), $id, $settings, $existingMethodNames);
        }
    }

    /**
     * @param string[] $existingMethodNames
     * @param Dependency[] $dependencies
     */
    private function collectMarkerDependencies(
        AbstractElement $element,
        Settings $settings,
        array &$existingMethodNames,
        array &$dependencies
    ): void {
        if (!$element instanceof PageSnippet) {
            return;
        }

        foreach ($element->getEditables() as $editable) {
            if (!$editable instanceof Image) {
                continue;
            }

            /** @var array<int, array<string, mixed>> $hotspots */
            $hotspots = $editable->getHotspots();
            $this->collectFromMarkerData($hotspots, $settings, $existingMethodNames, $dependencies);
            /** @var array<int, array<string, mixed>> $marker */
            $marker = $editable->getMarker();
            $this->collectFromMarkerData($marker, $settings, $existingMethodNames, $dependencies);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @param string[] $existingMethodNames
     * @param Dependency[] $dependencies
     */
    private function collectFromMarkerData(
        array $items,
        Settings $settings,
        array &$existingMethodNames,
        array &$dependencies
    ): void {
        foreach ($items as $item) {
            if (!is_array($item)) {
                throw new LogicException('Invalid marker data type (array expected)');
            }
            $data = $item['data'] ?? null;

            if (!is_iterable($data)) {
                continue;
            }

            foreach ($data as $markerHotspotItem) {
                if (!$markerHotspotItem instanceof MarkerHotspotItem) {
                    continue;
                }

                if (!$this->isMigratableReference($markerHotspotItem)) {
                    continue;
                }

                if (!is_int($markerHotspotItem->getValue())) {
                    throw new LogicException('Invalid marker hotspot item value type (integer expected)');
                }

                try {
                    $dependencies[] = $this->createDependency(MigrationType::fromString($markerHotspotItem->getType()), $markerHotspotItem->getValue(), $settings, $existingMethodNames);
                } catch (NotFoundException) {
                    // ignore missing references
                }
            }
        }
    }

    private function isMigratableReference(MarkerHotspotItem $item): bool
    {
        return in_array($item->getType(), ['document', 'asset', 'object'], true)
            && is_int($item->getValue());
    }

    /**
     * @param string[] $existingMethodNames
     * @throws NotFoundException
     */
    private function createDependency(MigrationType $type, int $id, Settings $settings, array &$existingMethodNames): Dependency
    {
        $dependency = $this->objectLoader->loadObject($type, $id);
        $methodName = $this->methodNameConverter->convert($dependency);
        $variableName = $this->variableNameConverter->convert($dependency);
        $code = null;
        if (!in_array($methodName, $existingMethodNames, true)) {
            $existingMethodNames[] = $methodName;
            $code = $this->getCodeGeneratorFactory()
                ->getCodeGenerator($type)
                ->generateCode($dependency, $settings->forDependencies(), $existingMethodNames);
        }

        return new Dependency($dependency, $variableName, $methodName, $code);
    }
}
