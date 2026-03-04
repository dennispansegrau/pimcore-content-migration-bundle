<?php

namespace PimcoreContentMigration\Generator\Dependency;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Pimcore\Model\Element\AbstractElement;
use Traversable;

use function count;

/**
 * @implements IteratorAggregate<Dependency>
 */
class DependencyList implements IteratorAggregate, Countable
{
    /**
     * @var Dependency[]
     */
    private array $dependencies = [];

    /**
     * @param Dependency[] $dependencies
     */
    public function __construct(array $dependencies = [])
    {
        foreach ($dependencies as $dependency) {
            $this->add($dependency);
        }
    }

    public function add(Dependency $dependency): void
    {
        if ($this->getByTypeAndId($dependency->getType(), $dependency->getId())) {
            return;
        }
        $this->dependencies[] = $dependency;
    }

    public function getDependency(AbstractElement $object): ?Dependency
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getTarget()->getId() === $object->getId() &&
                $dependency->getTarget()->getType() === $object->getType()) {
                return $dependency;
            }
        }

        return null;
    }

    public function getByTypeAndId(string $type, int $id): ?Dependency
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getType() === $type && $dependency->getId() === $id) {
                return $dependency;
            }
        }

        return null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->dependencies);
    }

    public function count(): int
    {
        return count($this->dependencies);
    }
}
