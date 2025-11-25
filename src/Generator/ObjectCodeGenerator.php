<?php

namespace PimcoreContentMigration\Generator;

use InvalidArgumentException;
use Pimcore\Model\DataObject;
use PimcoreContentMigration\Builder\DataObject\ConcreteBuilder;
use PimcoreContentMigration\Builder\DataObject\DataObjectBuilder;
use PimcoreContentMigration\Builder\DataObject\FolderBuilder;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Converter\AbstractElementToVariableNameConverter;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use ReflectionClass;
use ReflectionMethod;

class ObjectCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    /** setters to ignore */
    private const SETTER_FILTERS = [
        'setId',
        'setPublished',
        'setDao',
        'setDoNotRestoreKeyAndPath',
        'setDisableDirtyDetection',
        'setInDumpState',
    ];

    public function __construct(
        private readonly CodeGenerator $codeGenerator,
        AbstractElementToMethodNameConverter $methodNameConverter,
        AbstractElementToVariableNameConverter $variableNameConverter,
        ObjectLoaderInterface $objectLoader
    ) {
        parent::__construct(
            $methodNameConverter,
            $variableNameConverter,
            $objectLoader
        );
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
            'dependencies' => $this->getDependencies($settings, $abstractElement, $existingMethodNames),
            'isConcrete' => $abstractElement instanceof DataObject\Concrete,
            'setters' => $this->getSetters($abstractElement),
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

    /**
     * @param DataObject $abstractElement
     * @return array<string, string>
     * @throws \ReflectionException
     */
    private function getSetters(DataObject $abstractElement): array
    {
        $reflection = new ReflectionClass($abstractElement::class);
        $setters = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), 'set') && $method->getNumberOfParameters() === 1) {
                if (in_array($method->getName(), self::SETTER_FILTERS, true)) {
                    continue;
                }

                $setterName = $method->getName();
                $getterName = 'g' . substr($setterName, 1);
                $name = lcfirst(substr($setterName, 3));
                $setters[$name] = $abstractElement->$getterName();
            }
        }
        return $setters;
    }
}
