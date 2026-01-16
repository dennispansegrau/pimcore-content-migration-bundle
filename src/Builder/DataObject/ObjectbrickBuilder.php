<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

class ObjectbrickBuilder
{
    private ?Objectbrick $objectbrick = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $fieldName
     * @param Concrete $owner
     * @return static
     */
    public static function create(string $fieldName, Concrete $owner): static
    {
        $builder = new static();
        $builder->objectbrick = new Objectbrick($owner, $fieldName);
        return $builder;
    }

    public function getObject(): Objectbrick
    {
        if (!$this->objectbrick instanceof Objectbrick) {
            throw new LogicException('Objectbrick object has not been set');
        }
        return $this->objectbrick;
    }

    /**
     * @param AbstractData[] $items
     * @return $this
     */
    public function setItems(array $items): static
    {
        $this->getObject()->setItems($items);
        return $this;
    }
}
