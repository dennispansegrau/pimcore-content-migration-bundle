<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ObjectMetadata;

class ObjectMetadataBuilder
{
    private ?ObjectMetadata $objectMetadata = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $fieldName
     * @param array<int, string> $columns
     * @param Concrete $object
     * @return static
     */
    public static function create(string $fieldName, array $columns, Concrete $object): static
    {
        $builder = new static();
        $builder->objectMetadata = new ObjectMetadata($fieldName, $columns, $object);
        return $builder;
    }

    public function getObject(): ObjectMetadata
    {
        if (!$this->objectMetadata instanceof ObjectMetadata) {
            throw new LogicException('ObjectMetadata object has not been set');
        }
        return $this->objectMetadata;
    }

    /**
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->getObject()->setData($data);
        return $this;
    }
}
