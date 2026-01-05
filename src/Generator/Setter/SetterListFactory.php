<?php

namespace PimcoreContentMigration\Generator\Setter;

use function in_array;
use function lcfirst;

use Pimcore\Model\DataObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use function str_starts_with;
use function substr;

class SetterListFactory
{
    /** setters to ignore */
    private const SETTER_FILTERS = [
        'setId',
        'setPublished',
        'setDao',
        'setDoNotRestoreKeyAndPath',
        'setDisableDirtyDetection',
        'setInDumpState',
        'setParent',
        'setType',
        'setKey',
        'setParentId',
        'setHideUnpublished',
        'setVersions',
        'setVersionCount',
        'setClass',
        'setClassName',
        'setOmitMandatoryCheck',
        'setGetInheritedValues',
        'setClassId',
        'setPath',
        'setUserModification',
        'setCreationDate',
        'setModificationDate',
        'setUserOwner',
        'setLocked',
        'setProperties',
        'setScheduledTasks',
    ];

    /**
     * @param DataObject $abstractElement
     * @return SetterList
     * @throws ReflectionException
     */
    public function getList(DataObject $abstractElement): SetterList
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
                $setter = new Setter(
                    $name,
                    $abstractElement->$getterName()
                );
                $setters[$name] = $setter;
            }
        }
        return new SetterList($setters);
    }
}
