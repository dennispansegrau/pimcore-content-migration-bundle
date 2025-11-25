<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject;

class ConcreteBuilder extends DataObjectBuilder
{
    public function setPublished(bool $published): static
    {
        $this->getObject()->setPublished($published);
        return $this;
    }

    public function getObject(): DataObject\Concrete
    {
        if (!$this->dataObject instanceof DataObject\Concrete) {
            throw new LogicException('DataObject\Concrete object has not been set');
        }
        return $this->dataObject;
    }
}
