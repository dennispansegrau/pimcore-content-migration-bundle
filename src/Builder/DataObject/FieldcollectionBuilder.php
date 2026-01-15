<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class FieldcollectionBuilder
{
    private ?Fieldcollection $fieldcollection = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $property
     * @param AbstractData[] $abstractData
     * @return static
     * @throws \Exception
     */
    public static function create(string $property, array $abstractData): static
    {
        $builder = new static();
        $builder->fieldcollection = new Fieldcollection($abstractData, $property);
        return $builder;
    }

    public function getObject(): Fieldcollection
    {
        if (!$this->fieldcollection instanceof Fieldcollection) {
            throw new \LogicException('Fieldcollection object has not been set');
        }
        return $this->fieldcollection;
    }
}
