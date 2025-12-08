<?php

namespace PimcoreContentMigration\Generator;

use Pimcore\Model\Document\Editable\Image;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Model\Element\Data\MarkerHotspotItem;
use Pimcore\Model\Exception\NotFoundException;
use function in_array;
use function is_int;
use function is_string;

use LogicException;
use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Converter\AbstractElementToVariableNameConverter;
use PimcoreContentMigration\Factory\CodeGeneratorFactoryInterface;
use PimcoreContentMigration\Generator\Dependency\Dependency;
use PimcoreContentMigration\Generator\Dependency\DependencyList;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\MigrationType;
use RuntimeException;

abstract class AbstractElementCodeGenerator
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
    protected function getDependencies(Settings $settings, AbstractElement $abstractElement, array &$existingMethodNames): DependencyList
    {
        $dependencies = [];
        if ($settings->withDependencies() && $abstractElement->getDependencies()->getRequiresTotalCount() > 0) {
            /** @var array<string, string|int> $dependencyData */
            foreach ($abstractElement->getDependencies()->getRequires() as $dependencyData) {
                if (!isset($dependencyData['type'], $dependencyData['id']) || !is_string($dependencyData['type']) || !is_int($dependencyData['id'])) {
                    throw new LogicException('Invalid dependency data (string type and integer id expected)');
                }
                $dependencies[] = $this->createDependency(MigrationType::fromString($dependencyData['type']), $dependencyData['id'], $settings, $existingMethodNames);
            }
        }
        // add MarkerHotspotItems as Dependencies
        if ($abstractElement instanceof PageSnippet) {
            foreach ($abstractElement->getEditables() as $editable) {
                if (!$editable instanceof Image) {
                    continue;
                }
                foreach ($editable->getHotspots() as $hotspot) {
                    if (!is_iterable($hotspot['data'])) {
                        continue;
                    }
                    /** @var MarkerHotspotItem $markerHotspotItem */
                    foreach ($hotspot['data'] as $markerHotspotItem) {
                        if (in_array($markerHotspotItem->getType(), ['document', 'asset', 'object'], true) && is_int($markerHotspotItem->getValue())) {
                            try {
                                $dependencies[] = $this->createDependency(MigrationType::fromString($markerHotspotItem->getType()), $markerHotspotItem->getValue(), $settings, $existingMethodNames);
                            } catch (NotFoundException $e) {
                                // nothing to do
                            }
                        }
                    }
                }
                foreach ($editable->getMarker() as $marker) {
                    if (!is_iterable($marker['data'])) {
                        continue;
                    }
                    /** @var MarkerHotspotItem $markerHotspotItem */
                    foreach ($marker['data'] as $markerHotspotItem) {
                        if (in_array($markerHotspotItem->getType(), ['document', 'asset', 'object'], true) && is_int($markerHotspotItem->getValue())) {
                            try {
                                $dependencies[] = $this->createDependency(MigrationType::fromString($markerHotspotItem->getType()), $markerHotspotItem->getValue(), $settings, $existingMethodNames);
                            } catch (NotFoundException $e) {
                                // nothing to do
                            }
                        }
                    }
                }
            }
        }
        return new DependencyList($dependencies);
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
