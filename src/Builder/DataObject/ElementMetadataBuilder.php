<?php

namespace PimcoreContentMigration\Builder\DataObject;

use LogicException;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\Element\ElementInterface;

class ElementMetadataBuilder
{
    private ?ElementMetadata $elementMetadata = null;

    final protected function __construct()
    {
    }

    /**
     * @param string $fieldName
     * @param array<int, string> $columns
     * @param ElementInterface $element
     * @return static
     * @throws \Exception
     */
    public static function create(string $fieldName, array $columns, ElementInterface $element): static
    {
        $builder = new static();
        $builder->elementMetadata = new ElementMetadata($fieldName, $columns, $element);
        return $builder;
    }

    public function getObject(): ElementMetadata
    {
        if (!$this->elementMetadata instanceof ElementMetadata) {
            throw new LogicException('ElementMetadata object has not been set');
        }
        return $this->elementMetadata;
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
