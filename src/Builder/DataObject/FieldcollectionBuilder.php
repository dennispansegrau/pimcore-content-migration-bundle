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
    /** @var Fieldcollection<TItem>|null */
    private ?Fieldcollection $fieldCollection = null;

    final protected function __construct()
    {
    }

    /**
     * @throws Exception
     * @return static<TItem>
     */
    public static function create(): static
    {
        /** @var static<TItem> $builder */
        $builder = new static();
        /** @var Fieldcollection<TItem> $fieldCollection */
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
     * @return Fieldcollection<TItem>
     */
    public function getObject(): Fieldcollection
    {
        if (!$this->fieldCollection instanceof Fieldcollection) {
            throw new LogicException('Fieldcollection object has not been set');
        }
        return $this->fieldCollection;
    }
}
