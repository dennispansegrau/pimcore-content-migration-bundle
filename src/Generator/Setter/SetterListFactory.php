<?php

namespace PimcoreContentMigration\Generator\Setter;

use Pimcore\Model\DataObject;
use ReflectionException;

use function ucfirst;

class SetterListFactory
{
    /**
     * @param DataObject $abstractElement
     * @return SetterList
     * @throws ReflectionException
     */
    public function getList(DataObject $abstractElement): SetterList
    {
        if (!method_exists($abstractElement, 'getClass')) {
            throw new ReflectionException('DataObject must have a getClass method to generate setter list.');
        }
        /** @var DataObject\ClassDefinition $classDefinition */
        $classDefinition = $abstractElement->getClass();
        $fieldDefinitions = $classDefinition->getFieldDefinitions();

        $setters = [];
        foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
            $getterName = 'get' . ucfirst($fieldName);
            $setters[$fieldName] = new Setter(
                $fieldName,
                $abstractElement->$getterName()
            );
        }
        return new SetterList($setters);
    }
}
