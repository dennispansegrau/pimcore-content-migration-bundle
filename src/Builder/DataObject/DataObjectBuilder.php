<?php

namespace PimcoreContentMigration\Builder\DataObject;

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
     * @throws Exception
     */
    public static function findOrCreate(string $path, string $dataObjectClass): static
    {
        $builder = new static();

        $builder->dataObject = DataObject::getByPath($path);
        if (!$builder->dataObject instanceof DataObject) {
            $builder->dataObject = new $dataObjectClass();
            $key = basename($path);
            $builder->dataObject->setKey($key);
            $parentPath = dirname($path);
            $parent = $builder->getParentByPath($parentPath);
            $builder->dataObject->setParent($parent);
        }

        if (!$builder->dataObject instanceof $dataObjectClass) {
            //TODO
            $t = 1;
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

    /**
     * @throws Exception
     */
    private function getParentByPath(string $parentPath): DataObject
    {
        $parent = null;
        if (DataObject\Service::pathExists($parentPath)) {
            $parent = DataObject::getByPath($parentPath);
        }

        if ($parent === null) {
            $parent = DataObject\Service::createFolderByPath($parentPath);
        }

        if (!$parent instanceof DataObject) {
            throw new Exception("Parent data object not found for path: $parentPath");
        }

        return $parent;
    }
}
