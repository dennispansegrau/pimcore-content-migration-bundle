<?php

namespace PimcoreContentMigration\Generator\Setter;

use Exception;
use Pimcore\Model\DataObject;

use function ucfirst;

class SetterListFactory
{
    /**
     * @param DataObject $abstractElement
     * @return SetterList
     * @throws Exception
     */
    public function getList(DataObject $abstractElement): SetterList
    {
        if (!$abstractElement instanceof DataObject\Concrete) {
            return new SetterList([]);
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
