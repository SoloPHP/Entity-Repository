# Solo EntityRepository 📦

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/solophp/entity-repository)
[![PHP](https://img.shields.io/badge/php-8.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

**EntityRepository** - A modern, type-safe implementation of the Repository pattern for PHP domain entities. Provides a robust data access layer with automatic entity mapping and collection support.

## ✨ Features

- **Entity-centric design** - Work with domain objects instead of raw arrays
- **Automatic mapping** - Convert database results to entity objects via `toEntity()`  
- **Typed collections** - Customizable collection classes via constructor
- **Transaction management** - Atomic operations with `beginTransaction()`, `commit()`, and `rollback()`
- **Query Builder** - Flexible SQL construction with type safety
- **Generics support** - PHPDoc template types for IDE autocompletion
- **Strict type validation** - Ensures data integrity and type safety

## 📥 Installation

```sh
composer require solophp/entity-repository
```

## 🔗 Dependencies

- `solophp/database`: Database abstraction layer
- `solophp/query-builder`: SQL query construction

## 🚀 Quick Start

### 1. Define Your Entity

```php
class User implements EntityInterface {
    public function __construct(
        public readonly int $id,
        public string $name,
        public string $email,
        public DateTimeImmutable $createdAt
    ) {}
}
```

### 2. Create Repository

```php
/**
 * @extends EntityRepository<User>
 */
class UserRepository extends EntityRepository {
    public function __construct(Database $db) {
        parent::__construct(
            db: $db,
            table: 'users',
            collectionClass: UserCollection::class
        );
    }

    protected function toEntity(array $data): User {
        return new User(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            new DateTimeImmutable($data['created_at'])
        );
    }
}
```

---

## 📚 Core Features

### Repository Methods
### Entity Operations
| Method | Returns | Description |
|--------|---------|-------------|
| `findAll()` | `Collection<T>` | Retrieves all entities |
| `findById()` | `?T` | Finds an entity by ID |
| `findByIds()` | `Collection<T>` | Finds multiple entities by IDs |
| `findBy()` | `Collection<T>` | Finds entities based on criteria |
| `findOneBy()` | `?T` | Finds a single entity by criteria |

### Persistence
| Method | Description |
|--------|-------------|
| `create()` | Inserts a new entity |
| `bulkInsert()` | Batch insert of multiple entities |
| `update()` | Updates an existing entity |
| `delete()` | Removes an entity |

### Utilities
| Method | Returns | Description |
|--------|---------|-------------|
| `count()` | `int` | Returns total entity count |
| `exists()` | `bool` | Checks if an entity exists |
| `paginate()` | `Collection<T>` | Returns paginated results |

---

## 🧩 GenericCollection Methods

### Core Functionality
| Method | Description |
|--------|-------------|
| `__construct(array $items = [])` | Initialize with entity array |
| `getIterator()` | Get iterator for foreach loops |
| `count()` | Count items in collection |
| `toArray()` | Convert to native PHP array |

### Item Manipulation
| Method | Description |
|--------|-------------|
| `add(EntityInterface $item)` | Add entity to collection |
| `remove(EntityInterface $item)` | Remove entity from collection |
| `offsetGet(mixed $offset)` | Access item by index |

### Data Processing
| Method | Description |
|--------|-------------|
| `filter(callable $callback)` | Filter items using callback |
| `map(callable $callback)` | Transform items using callback |
| `pluck(string $property)` | Extract property values |
| `groupBy(callable $callback)` | Group items by criteria |
| `indexBy(string $property)` | Create property-indexed array |

### Sorting
| Method | Description |
|--------|-------------|
| `sortBy(callable $callback, bool $ascending = true)` | Custom comparison sort |
| `sortByProperty(string $property, bool $ascending = true)` | Property-based sort |

### Utility Methods
| Method | Description |
|--------|-------------|
| `first()` | Get first item |
| `last()` | Get last item |
| `jsonSerialize()` | JSON serialization support |

---

## 💡 Usage Examples

### Collection Operations
```php
$users = $userRepo->findAll();

// Filtering and sorting
$activeUsers = $users
    ->filter(fn(User $u) => $u->isActive())
    ->sortByProperty('name');

// Data extraction
$userEmails = $users->pluck('email');
$groupedByRole = $users->groupBy(fn(User $u) => $u->role);
```

### Custom Collections
```php
// Instantiate with custom collection
$repo = new UserRepository(
    db: $database,
    table: 'users',
    collectionClass: UserCollection::class
);

// Direct collection manipulation
$collection = new GenericCollection();
$collection->add($user1);
$collection->remove($user2);
```

---

## ⚙️ Advanced Configuration

### Custom Repository Setup
Configure repositories with granular control using constructor parameters:

```php
/**
 * @extends EntityRepository<Product>
 */
class ProductRepository extends EntityRepository {
    public function __construct(
        Database $db,
        string $collectionClass = ProductCollection::class
    ) {
        parent::__construct(
            db: $db,
            table: 'products',
            alias: 'p',
            primaryKey: 'prod_id',
            collectionClass: $collectionClass
        );
    }
}
```

**Key Parameters:**
- `table` (Required): Database table name
- `alias`: Table alias for complex queries
- `primaryKey`: Custom ID field for entity lookups
- `collectionClass`: Custom collection implementation

---

### Transaction Management Patterns
Implement robust data operations with ACID compliance:

**Basic Transaction**
```php
$repo->beginTransaction();

try {
    // Atomic operations
    $repo->update(1, ['stock' => $newStock]);
    $repo->create($orderData);
    $repo->commit();
} catch (DatabaseException $e) {
    $repo->rollback();
    throw new OperationFailedException("Transaction aborted", 0, $e);
}
```

---

## ⚙️ Requirements

- PHP 8.2+

## 🤝 Contributions

Feel free to open an issue or submit a pull request!

## 📝 License

This project is licensed under the [MIT License](LICENSE).

