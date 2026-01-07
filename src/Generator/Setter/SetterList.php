<?php

namespace PimcoreContentMigration\Generator\Setter;

use ArrayIterator;

use function count;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Setter>
 */
final class SetterList implements IteratorAggregate, Countable
{
    /** @var list<Setter> */
    private array $items = [];

    /**
     * @param iterable<Setter> $setters
     */
    public function __construct(iterable $setters = [])
    {
        foreach ($setters as $setter) {
            $this->items[] = $setter;
        }
    }

    public function add(Setter $setter): self
    {
        $this->items[] = $setter;
        return $this;
    }

    /**
     * @return Traversable<int, Setter>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
