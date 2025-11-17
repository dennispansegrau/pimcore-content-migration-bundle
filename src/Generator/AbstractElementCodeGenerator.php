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

    protected function getDependencies(Settings $settings, AbstractElement $object, array &$existingMethodNames): array
    {
        $dependencies = [];
        if ($settings->withDependencies() && $object->getDependencies()->getRequiresTotalCount() > 0) {
            foreach ($object->getDependencies()->getRequires() as $dependencyData) {
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
