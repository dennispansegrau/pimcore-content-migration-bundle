<?php

namespace PimcoreContentMigration\Builder\DataObject;

use Pimcore\Model\DataObject\Concrete;
use function basename;
use function dirname;

use Exception;

use function get_class;

use LogicException;

use function method_exists;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\DuplicateFullPathException;
use PimcoreContentMigration\Builder\AbstractElementBuilder;

use function ucfirst;

class DataObjectBuilder extends AbstractElementBuilder
{
    protected ?DataObject $dataObject = null;

    /**
     * @param string $path
     * @param class-string<DataObject> $dataObjectClass
     * @return static
     * @throws DuplicateFullPathException
     */
    public static function findOrCreate(string $path, string $dataObjectClass): static
    {
        $builder = new static();

        $builder->dataObject = $dataObjectClass::getByPath($path);
        if (!$builder->dataObject instanceof $dataObjectClass) {
            $builder->dataObject = new $dataObjectClass();
            $key = basename($path);
            $builder->dataObject->setKey($key);

            $parentPath = dirname($path);
            $parent = DataObject::getByPath($parentPath);
            if (!$parent instanceof DataObject) {
                throw new Exception("Parent data object not found for path: $parentPath");
            }
            $builder->dataObject->setParentId($parent->getId());
        }

        $builder->dataObject->save(); // must be already saved for some actions

        return $builder;
    }

    public function getObject(): DataObject
    {
        if (null === $this->dataObject) {
            throw new LogicException('DataObject object has not been set');
        }
        return $this->dataObject;
    }

    /**
     * @param array<string, string> $parameters
     * @return $this
     * @throws DuplicateFullPathException
     */
    public function save(array $parameters = []): static
    {
        $this->getObject()->save($parameters);
        return $this;
    }

    public function set(string $property, mixed $value): static
    {
        $setter = 'set' . ucfirst($property);

        if (method_exists($this->getObject(), $setter)) {
            $this->getObject()->$setter($value);
        } else {
            throw new Exception("Setter $setter not found in " . get_class($this->getObject()));
        }

        return $this;
    }
}
