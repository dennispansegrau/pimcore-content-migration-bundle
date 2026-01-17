<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Concrete;

class ClassificationstoreBuilder
{
    private ?Classificationstore $classificationstore = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $fieldName
     * @param Concrete $owner
     * @param array<int, array<int, array<string, mixed>>> $items
     * @return static
     */
    public static function create(string $fieldName, Concrete $owner, array $items): static
    {
        $builder = new static();
        $builder->classificationstore = new Classificationstore();
        $builder->classificationstore->setFieldname($fieldName);
        $builder->classificationstore->setObject($owner);

        $activeGroups = [];
        foreach ($items as $groupId => $keys) {
            $activeGroups[$groupId] = true;

            foreach ($keys as $keyId => $languages) {
                foreach ($languages as $language => $value) {
                    $builder->classificationstore->setLocalizedKeyValue($groupId, $keyId, $value, $language);
                }
            }
        }

        $builder->classificationstore->setActiveGroups($activeGroups);
        return $builder;
    }

    public function getObject(): Classificationstore
    {
        if (!$this->classificationstore instanceof Classificationstore) {
            throw new LogicException('Classificationstore object has not been set');
        }
        return $this->classificationstore;
    }
}
