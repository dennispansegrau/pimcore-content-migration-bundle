<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;

use Pimcore\Model\DataObject\Localizedfield;
use function get_class;

use LogicException;

use function method_exists;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

use function ucfirst;

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
