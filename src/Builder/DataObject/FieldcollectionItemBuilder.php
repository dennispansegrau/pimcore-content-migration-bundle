<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class FieldcollectionItemBuilder
{
    private ?AbstractData $item = null;

    final protected function __construct()
    {
    }

    /***
     * @template T of AbstractData
     * @param class-string<T> $classname
     * @return static
     * @throws \Exception
     */
    public static function create(string $classname): static
    {
        $builder = new static();
        $builder->item = new $classname();
        return $builder;
    }

    public function getObject(): AbstractData
    {
        if (!$this->item instanceof AbstractData) {
            throw new \LogicException('AbstractData object has not been set');
        }
        return $this->item;
    }

    public function set(string $property, mixed $value): static
    {
        $setter = 'set' . ucfirst($property);

        if (method_exists($this->getObject(), $setter)) {
            $this->getObject()->$setter($value);
        } else {
            throw new \RuntimeException("Setter $setter not found in " . get_class($this->getObject()));
        }

        return $this;
    }
}
