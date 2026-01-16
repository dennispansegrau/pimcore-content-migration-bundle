<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Exception;

use function get_class;

use LogicException;

use function method_exists;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use RuntimeException;

use function ucfirst;

class ObjectbrickItemBuilder
{
    private ?AbstractData $item = null;

    final protected function __construct()
    {
    }

    /**
     * @template T of AbstractData
     * @param class-string<T> $classname
     * @return static
     * @throws Exception
     */
    public static function create(string $classname, Concrete $concrete): static
    {
        $builder = new static();
        $builder->item = new $classname($concrete);
        return $builder;
    }

    public function getObject(): AbstractData
    {
        if (!$this->item instanceof AbstractData) {
            throw new LogicException('Objectbrick AbstractData object has not been set');
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
