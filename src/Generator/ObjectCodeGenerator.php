<?php

namespace PimcoreContentMigration\Generator;

use PimcoreContentMigration\Generator\Setter\Setter;
use PimcoreContentMigration\Generator\Setter\SetterListFactory;
use function get_class;
use function in_array;

use InvalidArgumentException;

use function lcfirst;

use Pimcore\Model\DataObject;
use PimcoreContentMigration\Builder\DataObject\ConcreteBuilder;
use PimcoreContentMigration\Builder\DataObject\DataObjectBuilder;
use PimcoreContentMigration\Builder\DataObject\FolderBuilder;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Generator\Dependency\DependencyCollector;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use function str_starts_with;
use function substr;

class ObjectCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly CodeGenerator $codeGenerator,
        public DependencyCollector $dependencyCollector,
        public SetterListFactory $setterListFactory,
        private readonly AbstractElementToMethodNameConverter $methodNameConverter,
    ) {
    }

    /**
     * @param object $abstractElement
     * @param Settings $settings
     * @param string[] $existingMethodNames
     * @return string
     */
    public function generateCode(object $abstractElement, Settings $settings, array &$existingMethodNames = []): string
    {
        if (!$abstractElement instanceof DataObject) {
            throw new InvalidArgumentException();
        }

        $methodName = $this->methodNameConverter->convert($abstractElement);
        if (empty($existingMethodNames)) {
            $existingMethodNames[] = $methodName;
        }

        return $this->codeGenerator->generate('object_template', [
            'object' => $abstractElement,
            'type' => $abstractElement->getType(),
            'classname' => '\\' . get_class($abstractElement),
            'builder' => $this->getBuilderClass($abstractElement),
            'methodName' => $methodName,
            'settings' => $settings,
            'dependencies' => $this->dependencyCollector->getDependencies($settings, $abstractElement, $existingMethodNames),
            'setters' => $this->setterListFactory->getList($abstractElement),
            'isConcrete' => $abstractElement instanceof DataObject\Concrete,
        ]);
    }

    private function getBuilderClass(DataObject $abstractElement): string
    {
        if ($abstractElement instanceof DataObject\Folder) {
            return '\\' . FolderBuilder::class ;
        } elseif ($abstractElement instanceof DataObject\Concrete) {
            return '\\' . ConcreteBuilder::class;
        } else {
            return '\\' . DataObjectBuilder::class;
        }
    }
}
