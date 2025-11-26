<?php

namespace PimcoreContentMigration\Generator\Dependency;

use ArrayIterator;

use function count;

use Countable;
use IteratorAggregate;
use Traversable;

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
        $this->dependencies[] = $dependency;
    }

    public function getDependency(object $object): ?Dependency
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getTarget() === $object) {
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
