<?php

namespace PimcoreContentMigration\Builder;

use Pimcore\Model\Element\AbstractElement;

abstract class Builder
{
    final protected function __construct()
    {
    }

    abstract public function getObject(): AbstractElement;

    public function setProperty(
        string $name,
        string $type,
        mixed $data,
        bool $inherited = false,
        bool $inheritable = false
    ): static {
        $this->getObject()->setProperty($name, $type, $data, $inherited, $inheritable);
        return $this;
    }
}
