<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;
use LogicException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use RuntimeException;

use function class_exists;
use function get_class;
use function method_exists;
use function ucfirst;

class FieldcollectionItemBuilder
{
    private ?AbstractData $item = null;

    final protected function __construct()
    {
    }

    /**
     * @template T of AbstractData
     * @param class-string<T> $classname
     * @param Concrete $owner
     * @return static
     * @throws Exception
     */
    public static function create(string $classname, Concrete $owner): static
    {
        $builder = new static();
        if (!class_exists($classname)) {
            throw new Exception("Class $classname not found. You must transfer the var/classes and var/config directories before running the migration.");
        }
        $builder->item = new $classname();
        $builder->item->setObject($owner);
        return $builder;
    }

    public function getObject(): AbstractData
    {
        if (!$this->item instanceof AbstractData) {
            throw new LogicException('AbstractData object has not been set');
        }
        return $this->item;
    }

    public function set(string $property, mixed $value): static
    {
        $setter = 'set' . ucfirst($property);

        if (method_exists($this->getObject(), $setter)) {
            $this->getObject()->$setter($value);
        } else {
            throw new RuntimeException("Setter $setter not found in " . get_class($this->getObject()));
        }

        return $this;
    }
}
