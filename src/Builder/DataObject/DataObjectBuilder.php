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
            $parentPath = dirname($path);
            $key = basename($path);
            $builder->dataObject = $builder->createDataObject($dataObjectClass, $parentPath, $key);
        }

        // the object already exists but is not of the correct type
        if (!$builder->dataObject instanceof $dataObjectClass) {
            $parentPath = dirname($path);
            $tempKey = 'temp_'. basename($path) . '_' . random_int(1000, 9999);
            try {
                $tempObject = $builder->createDataObject($dataObjectClass, $parentPath, $tempKey);
                $builder->replaceObject($builder->dataObject, $tempObject);
            } catch (Exception $exception) {
                $tempObject = DataObject::getByPath($parentPath . '/' . $tempKey);
                $tempObject?->delete();
            }
        }

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

    /**
     * @throws Exception
     */
    private function createDataObject(string $dataObjectClass, string $parentPath, string $key): DataObject
    {
        $object = new $dataObjectClass();
        if (!$object instanceof DataObject) {
            throw new Exception("Class $dataObjectClass is not a DataObject");
        }
        $object->setKey(DataObject\Service::getValidKey($key, 'object'));
        $parent = $this->getParentByPath($parentPath);
        $object->setParent($parent);
        $object->save();
        return $object;
    }

    /**
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    private function replaceObject(DataObject $oldObject, DataObject $newObject): void
    {
        $children = $oldObject->getChildren();
        foreach ($children as $child) {
            if (!$child instanceof DataObject) {
                continue;
            }
            $child->setParent($newObject);
            $child->save();
        }

        $oldKey = $oldObject->getKey();
        $oldObject->delete();

        if ($oldKey === null) {
            throw new LogicException('Old object has no key');
        }
        $newObject->setKey($oldKey);
        $newObject->save();

        $this->dataObject = $newObject;
    }
}
