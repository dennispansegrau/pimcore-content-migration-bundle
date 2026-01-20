<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use LogicException;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

/**
 * @template TItem of AbstractData
 */
class FieldcollectionBuilder
{
    /**
     * @var Fieldcollection<TItem>|null
     */
    private ?Fieldcollection $fieldcollection = null;

    final protected function __construct()
    {
    }

    /**
     * @template TCreateItem of AbstractData
     * @param string $property
     * @param array<int, TCreateItem> $abstractData
     * @return FieldcollectionBuilder<TCreateItem>
     * @throws Exception
     */
    public static function create(string $property, array $abstractData): self
    {
        $builder = new static();
        /** @var Fieldcollection<TItem> $fieldcollection */
        $fieldcollection = new Fieldcollection($abstractData, $property);
        $builder->fieldcollection = $fieldcollection;
        return $builder;
    }

    /**
     * @return Fieldcollection<TItem>
     */
    public function getObject(): Fieldcollection
    {
        if (!$this->fieldcollection instanceof Fieldcollection) {
            throw new LogicException('Fieldcollection object has not been set');
        }
        return $this->fieldcollection;
    }
}
