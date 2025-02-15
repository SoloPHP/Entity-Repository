<?php declare(strict_types=1);

namespace Solo;

use Solo\EntityRepository\CollectionInterface;
use Solo\EntityRepository\EntityInterface;
use Solo\EntityRepository\GenericCollection;
use Solo\EntityRepository\RepositoryInterface;

/**
 * @template TEntity of EntityInterface
 * @implements RepositoryInterface<TEntity>
 */
abstract class EntityRepository implements RepositoryInterface
{
    protected QueryBuilder $queryBuilder;

    public function __construct(
        protected Database $db,
        protected string $table,
        protected ?string $alias = null,
        protected string $primaryKey = 'id',
        protected string $collectionClass = GenericCollection::class
    ) {
        $this->queryBuilder = new QueryBuilder($this->db, $this->table, $this->alias);
    }

    /**
     * @param array<string, mixed> $data
     * @return TEntity
     */
    abstract protected function toEntity(array $data): EntityInterface;

    /**
     * @param array<array<string, mixed>> $rows
     * @return array<int, TEntity>
     */
    protected function toEntities(array $rows): array
    {
        return array_map([$this, 'toEntity'], $rows);
    }

    /**
     * @param array<array<string, mixed>> $rows
     * @return CollectionInterface<TEntity>
     */
    protected function toCollection(array $rows): CollectionInterface
    {
        return new $this->collectionClass($this->toEntities($rows));
    }

    public function findAll(): CollectionInterface
    {
        return $this->toCollection(
            $this->queryBuilder->select()->get()
        );
    }

    public function findById(int|string $id): ?EntityInterface
    {
        $data = $this->queryBuilder->select()
            ->where($this->primaryKey, '=', $id)
            ->getOne();

        return $data ? $this->toEntity($data) : null;
    }

    public function findByIds(array $ids): CollectionInterface
    {
        return $this->toCollection(
            $this->queryBuilder->select()
                ->whereIn($this->primaryKey, $ids)
                ->get()
        );
    }

    public function findBy(array $criteria): CollectionInterface
    {
        $query = $this->queryBuilder->select();
        foreach ($criteria as $key => $value) {
            $query->where($key, '=', $value);
        }
        return $this->toCollection($query->get());
    }

    public function findOneBy(array $criteria): ?EntityInterface
    {
        $query = $this->queryBuilder->select();
        foreach ($criteria as $key => $value) {
            $query->where($key, '=', $value);
        }
        $data = $query->getOne();
        return $data ? $this->toEntity($data) : null;
    }

    public function findByLike(string|array $fields, string $pattern): CollectionInterface
    {
        $query = $this->queryBuilder->select();
        $query->whereGroup(function ($qb) use ($fields, $pattern) {
        foreach ((array)$fields as $field) {
                $qb->where($field, 'LIKE', "%$pattern%", 'OR');
        }
        });
        return $this->toCollection($query->get());
    }

    public function paginate(int $page = 1, int $limit = 10): CollectionInterface
    {
        return $this->toCollection(
            $this->queryBuilder->select()->paginate($page, $limit)->get()
        );
    }

    public function countBy(array $criteria = []): int
    {
        $query = $this->queryBuilder->select(['COUNT(*) as count']);
        foreach ($criteria as $key => $value) {
            $query->where($key, '=', $value);
        }
        return (int)($query->getOne()['count'] ?? 0);
    }

    public function create(array $data): bool
    {
        return $this->queryBuilder->insert($data);
    }

    public function bulkInsert(array $records): bool
    {
        if (empty($records)) {
            return false;
        }

        return $this->db->query(
                "INSERT INTO ?t (?p) VALUES ?p",
                $this->table,
                implode(',', array_keys($records[0])),
                array_map('array_values', $records)
            )->rowCount() > 0;
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->queryBuilder->update($data, $this->primaryKey, $id);
    }

    public function delete(int|string $id): bool
    {
        return $this->queryBuilder->delete($this->primaryKey, $id);
    }

    public function count(): int
    {
        return $this->queryBuilder->count();
    }

    public function exists(array $criteria): bool
    {
        return $this->findOneBy($criteria) !== null;
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollback();
    }
}