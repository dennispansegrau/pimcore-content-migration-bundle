<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use LogicException;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class FieldcollectionBuilder
{
    /** @var Fieldcollection<AbstractData>|null */
    private ?Fieldcollection $fieldCollection = null;

    final protected function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public static function create(): static
    {
        $builder = new static();
        $fieldCollection = new Fieldcollection();
        $builder->fieldCollection = $fieldCollection;
        return $builder;
    }

    public function addItem(AbstractData $item): static
    {
        $this->getObject()->add($item);
        return $this;
    }

    /**
     * @return Fieldcollection<AbstractData>
     */
    public function getObject(): Fieldcollection
    {
        if (!$this->fieldCollection instanceof Fieldcollection) {
            throw new LogicException('Fieldcollection object has not been set');
        }
        return $this->fieldCollection;
    }
}
