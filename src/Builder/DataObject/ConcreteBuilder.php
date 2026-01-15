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

    /**
     * @param array<string, array<string, mixed>> $items
     */
    public function setObjectbrick(string $property, string $fieldName, array $items): static
    {
        $setter = 'set' . ucfirst($property);
        $objectBrick = new DataObject\Objectbrick($this->getObject(), $fieldName);
        $data = [];
        foreach ($items as $classname => $item) {
            /** @var AbstractData $element */
            $element = new $classname($this->getObject());
            foreach ($item as $key => $value) {
                $element->set($key, $value);
            }
            $data[] = $element;
        }
        $objectBrick->setItems($data);

        if (method_exists($this->getObject(), $setter)) {
            $this->getObject()->$setter($objectBrick);
        } else {
            throw new Exception("Setter $setter not found in " . get_class($this->getObject()));
        }

        return $this;
    }
}
