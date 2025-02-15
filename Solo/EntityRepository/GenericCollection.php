<?php declare(strict_types=1);

namespace Solo\EntityRepository;

use ArrayIterator;
use InvalidArgumentException;

/**
 * @template T of EntityInterface
 * @implements CollectionInterface<T>
 */
class GenericCollection implements CollectionInterface
{
    /** @var array<int, T> */
    protected array $items;

    /**
     * Constructor for the collection.
     *
     * @param array<int, T> $items The initial items for the collection.
     * @throws InvalidArgumentException If any item does not implement EntityInterface.
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof EntityInterface) {
                throw new InvalidArgumentException(
                    'Collection items must implement EntityInterface'
                );
            }
        }
        $this->items = $items;
    }

    /**
     * Returns an iterator for iterating over the collection.
     *
     * @return ArrayIterator An iterator for the collection.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Returns the number of items in the collection.
     *
     * @return int The count of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns an array representation of the collection.
     *
     * @return array<int, T> The collection as an array.
     */
    public function toArray(): array
    {
        return array_map(fn(EntityInterface $item) => $item->toArray(), $this->items);
    }

    /**
     * Filters the collection based on a callback function.
     *
     * @param callable $callback A function that determines if an item should be included.
     * @return static<T> A new collection containing the filtered items.
     */
    public function filter(callable $callback): static
{
        return new static(array_filter($this->items, $callback));
    }

    /**
     * Applies a callback function to each item in the collection and returns a new collection.
     *
     * @template TMapResult of EntityInterface
     * @param callable(T): TMapResult $callback The function to apply to each item.
     * @return static<TMapResult> A new collection with the transformed items.
     */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * Returns the first item in the collection or null if the collection is empty.
     *
     * @return EntityInterface|null The first item or null.
     */
    public function first(): ?EntityInterface
    {
        return $this->items[0] ?? null;
    }

    /**
     * Returns the last item in the collection or null if it is empty.
     *
     * @return EntityInterface|null The last item or null.
     */
    public function last(): ?EntityInterface
    {
        return empty($this->items) ? null : $this->items[array_key_last($this->items)];
    }

    /**
     * Retrieves an item at a specific index.
     *
     * @param mixed $offset The index of the item.
     * @return EntityInterface|null The item at the given index or null if not found.
     */
    public function offsetGet(mixed $offset): ?EntityInterface
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Add an item to the collection.
     *
     * @param T $item The item to add
     */
    public function add(EntityInterface $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Remove an item from the collection.
     *
     * @param T $item The item to remove
     */
    public function remove(EntityInterface $item): void
    {
        foreach ($this->items as $key => $currentItem) {
            if ($currentItem === $item) {
                unset($this->items[$key]);
            }
        }
        $this->items = array_values($this->items);
    }

    /**
     * Sort the collection by the value returned from the callback.
     *
     * @param callable(T): mixed $callback
     * @param bool $ascending
     * @return static<T> A new collection with sorted items.
     */
    public function sortBy(callable $callback, bool $ascending = true): static
    {
        $items = $this->items;
        usort($items, function ($a, $b) use ($callback, $ascending) {
            $valueA = $callback($a);
            $valueB = $callback($b);
            return $ascending ? $valueA <=> $valueB : $valueB <=> $valueA;
        });
        return new static($items);
    }

    /**
     * Sort the collection by a property.
     *
     * @param string $property
     * @param bool $ascending
     * @return static<T> A new collection with sorted items.
     */
    public function sortByProperty(string $property, bool $ascending = true): static
    {
        return $this->sortBy(fn($item) => $item->{$property}, $ascending);
    }

    /**
     * Pluck a specific property from each item in the collection.
     *
     * @param string $property
     * @return static<T> A new collection with only items having the specified property.
     */
    public function pluck(string $property): static
    {
        $items = array_filter($this->items, fn($item) => isset($item->{$property}));
        return new static($items);
    }

    /**
     * Group items by a key generated by the callback.
     *
     * @param callable(T): string|int $callback
     * @return array<string|int, static<T>> An array of new collections grouped by callback result.
     */
    public function groupBy(callable $callback): array
    {
        $groups = [];
        foreach ($this->items as $item) {
            $key = $callback($item);
            if (!isset($groups[$key])) {
                $groups[$key] = new static([]);
            }
            $groups[$key]->add($item);
        }
        return $groups;
    }

    /**
     * Index the collection by a given property.
     *
     * @param string $property
     * @return static<T> A new collection with items indexed by the specified property.
     */
    public function indexBy(string $property): static
    {
        $items = [];
        foreach ($this->items as $item) {
            $key = $item->{$property} ?? null;
            if ($key !== null) {
                $items[$key] = $item;
            }
        }
        return new static($items);
    }

    /**
     * Serializes the collection to an array format suitable for JSON encoding.
     *
     * @return array An array representation of the collection.
     */
    public function jsonSerialize(): array
    {
        return array_map(
            fn(EntityInterface $item) => $item->toArray(),
            $this->items
        );
    }
}