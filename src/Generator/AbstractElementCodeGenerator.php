<?php

namespace PimcoreContentMigration\Generator;

use function in_array;

use Pimcore\Model\Element\AbstractElement;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Factory\CodeGeneratorFactoryInterface;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\MigrationType;
use RuntimeException;

abstract class AbstractElementCodeGenerator
{
    private ?CodeGeneratorFactoryInterface $codeGeneratorFactory = null;

    public function __construct(
        protected readonly AbstractElementToMethodNameConverter $methodNameConverter,
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
     * @return array<string, string|null>
     */
    protected function getDependencies(Settings $settings, AbstractElement $abstractElement, array &$existingMethodNames): array
    {
        $dependencies = [];
        if ($settings->withDependencies() && $abstractElement->getDependencies()->getRequiresTotalCount() > 0) {
            /** @var array<string, string|int> $dependencyData */
            foreach ($abstractElement->getDependencies()->getRequires() as $dependencyData) {
                if (!isset($dependencyData['type'], $dependencyData['id']) || !is_string($dependencyData['type']) || !is_int($dependencyData['id'])) {
                    throw new \LogicException('Invalid dependency data (string type and integer id expected)');
                }
                $dependency = $this->objectLoader->loadObject(MigrationType::fromString($dependencyData['type']), $dependencyData['id']);
                $methodName = $this->methodNameConverter->convert($dependency);
                $code = null;
                if (!in_array($methodName, $existingMethodNames, true)) {
                    $existingMethodNames[] = $methodName;
                    $code = $this->getCodeGeneratorFactory()
                        ->getCodeGenerator($settings->getType())
                        ->generateCode($dependency, $settings->forDependencies(), $existingMethodNames);
                }
                $dependencies[$methodName] = $code;
            }
        }
        return $dependencies;
    }
}
