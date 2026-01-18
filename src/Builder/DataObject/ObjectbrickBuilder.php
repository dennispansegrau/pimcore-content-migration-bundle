<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

use function ucfirst;

class ObjectbrickBuilder
{
    private ?Objectbrick $objectbrick = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $fieldName
     * @param array<string, AbstractData> $items
     * @param Concrete $owner
     * @return static
     */
    public static function create(string $fieldName, Concrete $owner, array $items): static
    {
        $builder = new static();
        $getter = 'get' . ucfirst($fieldName);
        $objectbrick = $owner->$getter();
        if (!$objectbrick instanceof Objectbrick) {
            throw new LogicException("Objectbrick $fieldName not found in object");
        }
        $builder->objectbrick = $objectbrick;
        foreach ($items as $property => $abstractData) {
            $setter = 'set' . $property;
            $builder->objectbrick->$setter($abstractData);
        }
        return $builder;
    }

    public function getObject(): Objectbrick
    {
        if (!$this->objectbrick instanceof Objectbrick) {
            throw new LogicException('Objectbrick object has not been set');
        }
        return $this->objectbrick;
    }
}
