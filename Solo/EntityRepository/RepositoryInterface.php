<?php declare(strict_types=1);

namespace Solo\EntityRepository;

/**
 * Interface representing a generic repository for entities.
 *
 * @template TEntity of EntityInterface
 */
interface RepositoryInterface
{
    /** @return CollectionInterface<TEntity> */
    public function findAll(): CollectionInterface;

    /**
     * Finds an entity by its ID.
     *
     * @param int|string $id The unique identifier of the entity.
     * @return TEntity|null The entity if found, or null otherwise.
     */
    public function findById(int|string $id): ?EntityInterface;

    /**
     * Finds multiple entities by their IDs.
     *
     * @param array<int|string> $ids The list of IDs to search for.
     * @return CollectionInterface<TEntity> A collection of matching entities.
     */
    public function findByIds(array $ids): CollectionInterface;

    /**
     * Finds entities based on given criteria.
     *
     * @param array<string, mixed> $criteria An associative array of field-value pairs to filter entities.
     * @return CollectionInterface<TEntity> A collection of matching entities.
     */
    public function findBy(array $criteria): CollectionInterface;

    /**
     * Finds a single entity based on given criteria.
     *
     * @param array<string, mixed> $criteria An associative array of field-value pairs to filter entities.
     * @return TEntity|null The first matching entity or null if none found.
     */
    public function findOneBy(array $criteria): ?EntityInterface;

    /**
     * Searches for entities where a field value matches a given pattern.
     *
     * @param string|array<string> $fields The field(s) to perform the search on.
     * @param string $pattern The search pattern (e.g., SQL LIKE '%pattern%').
     * @return CollectionInterface<TEntity> A collection of matching entities.
     */
    public function findByLike(string|array $fields, string $pattern): CollectionInterface;

    /**
     * Paginates the collection of entities.
     *
     * @param int $page The page number.
     * @param int $limit The number of results per page.
     * @return CollectionInterface<TEntity> A paginated collection of entities.
     */
    public function paginate(int $page = 1, int $limit = 10): CollectionInterface;

    /**
     * Counts the number of entities that match given criteria.
     *
     * @param array<string, mixed> $criteria An associative array of field-value pairs to filter entities.
     * @return int The number of matching entities.
     */
    public function countBy(array $criteria = []): int;

    /**
     * Creates a new entity with the given data.
     *
     * @param array<string, mixed> $data The data for creating the entity.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool;

    /**
     * Inserts multiple entities at once.
     *
     * @param array<int, array<string, mixed>> $records A list of records to insert.
     * @return bool True on success, false on failure.
     */
    public function bulkInsert(array $records): bool;

    /**
     * Updates an existing entity by its ID.
     *
     * @param int|string $id The ID of the entity to update.
     * @param array<string, mixed> $data The new data for the entity.
     * @return bool True on success, false on failure.
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Deletes an entity by its ID.
     *
     * @param int|string $id The ID of the entity to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int|string $id): bool;

    /**
     * Counts the total number of entities in the repository.
     *
     * @return int The total count of entities.
     */
    public function count(): int;

    /**
     * Checks if an entity matching the given criteria exists.
     *
     * @param array<string, mixed> $criteria The criteria to check.
     * @return bool True if an entity exists, false otherwise.
     */
    public function exists(array $criteria): bool;

    /**
     * Begins a database transaction.
     */
    public function beginTransaction(): void;

    /**
     * Commits the current transaction.
     */
    public function commit(): void;

    /**
     * Rolls back the current transaction.
     */
    public function rollback(): void;
}