<?php declare(strict_types=1);

namespace Solo\EntityRepository;

/**
 * Interface for all entities that support toArray().
 */
interface EntityInterface
{
    /**
     * Convert entity to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}