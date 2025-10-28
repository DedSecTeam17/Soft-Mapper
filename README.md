# Soft-Mapper

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%205.6-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-active-brightgreen)
![Version](https://img.shields.io/badge/version-2.0.0-orange)

A lightweight, powerful, and easy-to-use PHP ORM (Object-Relational Mapping) library for MySQL databases. Soft-Mapper provides a clean and intuitive interface for database operations with built-in security features and advanced ORM capabilities.

## 🚀 What's New in v2.0

Version 2.0 brings **27 new methods** and **advanced ORM features** that make Soft-Mapper comparable to Laravel's Eloquent:

- ✨ **Automatic Timestamps** - Auto-manage created_at/updated_at
- ✨ **Soft Deletes** - Mark records as deleted without removing them
- ✨ **Query Scopes** - Reusable query constraints
- ✨ **Batch Operations** - Insert multiple records efficiently
- ✨ **Transactions** - Full transaction support
- ✨ **Advanced Queries** - whereIn, whereBetween, whereNull, JOINs
- ✨ **Helper Methods** - count(), exists(), first(), pluck()
- ✨ **And much more!** - See [CHANGELOG.md](CHANGELOG.md) for complete list

**📚 Documentation:**
- [Quick Start Guide](QUICK_START.md) - Get started with new features
- [Changelog](CHANGELOG.md) - Complete list of changes
- [Advanced Examples](advanced-example.php) - Real-world usage examples

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating Models](#creating-models)
  - [Basic CRUD Operations](#basic-crud-operations)
  - [Advanced Queries](#advanced-queries)
  - [Aggregate Functions](#aggregate-functions)
- [Advanced Features](#advanced-features)
- [API Reference](#api-reference)
- [Examples](#examples)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)
- [Author](#author)

## Features

### Core Features
- ✅ **Simple and Intuitive API**: Easy-to-understand method chaining interface
- ✅ **Full CRUD Support**: Create, Read, Update, and Delete operations
- ✅ **Query Builder**: Build complex SQL queries with simple PHP methods
- ✅ **Security First**: Built-in prepared statements prevent SQL injection
- ✅ **Aggregate Functions**: Support for COUNT, SUM, AVG, MIN, MAX, etc.
- ✅ **Flexible Filtering**: WHERE and HAVING clauses with multiple conditions
- ✅ **Result Ordering**: ORDER BY with ASC/DESC support
- ✅ **Result Grouping**: GROUP BY for aggregate queries
- ✅ **Result Limiting**: LIMIT clause for pagination
- ✅ **Method Chaining**: Chain multiple methods for complex queries
- ✅ **PDO-Based**: Uses PHP Data Objects for database abstraction
- ✅ **Lightweight**: Minimal dependencies, single-file implementation

### Advanced Features ⭐ NEW
- ✨ **Automatic Timestamps**: Auto-manage created_at and updated_at columns
- ✨ **Soft Deletes**: Mark records as deleted without removing from database
- ✨ **Pagination with OFFSET**: Full pagination support with limit and offset
- ✨ **Custom Primary Keys**: Support for non-'id' primary key columns
- ✨ **Batch Operations**: Insert multiple records efficiently with insertMany()
- ✨ **Query Scopes**: Define reusable query constraints
- ✨ **JOIN Support**: INNER JOIN, LEFT JOIN, RIGHT JOIN operations
- ✨ **Advanced WHERE Clauses**: whereIn, whereNotIn, whereBetween, whereNull
- ✨ **Helper Methods**: first(), count(), exists(), pluck()
- ✨ **Transaction Support**: Complete transaction management
- ✨ **Raw Queries**: Execute custom SQL when needed
- ✨ **Chunking**: Memory-efficient processing of large datasets
- ✨ **Update or Create**: Intelligent upsert operations
- ✨ **Distinct Results**: Get unique records only

## Requirements

- **PHP**: Version 5.6 or higher (PHP 7.x or 8.x recommended)
- **MySQL**: Version 5.5 or higher
- **PHP Extensions**:
  - PDO
  - PDO_MySQL

## Installation

### Using Composer (Recommended)

Add to your `composer.json`:

```json
{
    "require": {
        "dedsecteam17/soft-mapper": "^2.0"
    }
}
```

Then run:

```bash
composer install
```

Or install directly:

```bash
composer require dedsecteam17/soft-mapper
```

### Manual Installation

1. Clone this repository or download the files:

```bash
git clone https://github.com/DedSecTeam17/Soft-Mapper.git
cd Soft-Mapper
```

2. Include the library in your project:

```php
require_once 'path/to/SoftMapper.php';
```

### Using as a Git Submodule

```bash
git submodule add https://github.com/DedSecTeam17/Soft-Mapper.git libs/soft-mapper
```

Then include in your code:

```php
require_once __DIR__ . '/libs/soft-mapper/SoftMapper.php';
```

## Quick Start

```php
<?php
// 1. Configure database connection
require_once 'env.php';

// 2. Create a model
require_once 'SoftMapper.php';

class User extends SoftMapper
{
    public $table_name = "users";
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
    }
}

// 3. Use the model
$user = new User();

// Insert a new user
$user->columns['name'] = 'John Doe';
$user->columns['email'] = 'john@example.com';
$user->insert();

// Fetch all users
$all_users = $user->all()->getAll();
```

## Configuration

### Database Configuration

Edit `env.php` to configure your database connection:

```php
<?php
define('host', 'localhost');      // Database host
define('dbname', 'your_database'); // Database name
define('user', 'your_username');   // Database username
define('password', 'your_password'); // Database password
```

### Environment-Specific Configuration

For production environments, consider using environment variables:

```php
<?php
define('host', getenv('DB_HOST') ?: 'localhost');
define('dbname', getenv('DB_NAME') ?: 'softmapper');
define('user', getenv('DB_USER') ?: 'root');
define('password', getenv('DB_PASSWORD') ?: '');
```

## Usage

### Creating Models

Each model represents a database table. Extend the `SoftMapper` class:

```php
<?php
require_once 'SoftMapper.php';

class Post extends SoftMapper
{
    public $table_name = "posts";  // Your table name
    public $columns = [];          // Array to hold column values

    public function __construct()
    {
        parent::__construct();
    }
}
```

### Basic CRUD Operations

#### Create (Insert)

Insert new records into the database:

```php
$post = new Post();
$post->columns['title'] = 'My First Post';
$post->columns['body'] = 'This is the content of my post.';
$post->columns['author_id'] = 1;
$post->columns['status'] = 'published';
$post->columns['created_at'] = date('Y-m-d H:i:s');

if ($post->insert()) {
    echo "Post created successfully!";
}
```

#### Read (Select)

**Find by ID:**

```php
$post = new Post();
$result = $post->find(1);

if ($result) {
    echo $result->title;
}
```

**Get All Records:**

```php
$post = new Post();
$all_posts = $post->all()->getAll();

foreach ($all_posts as $p) {
    echo $p->title . "<br>";
}
```

**Get Single Record:**

```php
$post = new Post();
$single_post = $post->all()->where([['id', '=', 1]])->get();
```

**Select Specific Columns:**

```php
$post = new Post();
$results = $post->select(['title', 'author_id', 'created_at'])->getAll();
```

#### Update

Update existing records:

```php
$post = new Post();
$post->columns['title'] = 'Updated Title';
$post->columns['body'] = 'Updated content';
$post->columns['updated_at'] = date('Y-m-d H:i:s');

$post->update()->where([['id', '=', 1]])->execute();
```

**Update Multiple Records:**

```php
$post = new Post();
$post->columns['status'] = 'archived';

$post->update()
    ->where([
        ['created_at', '<', '2023-01-01', 'AND'],
        ['status', '=', 'draft']
    ])
    ->execute();
```

#### Delete

Delete records from the database:

```php
$post = new Post();
$post->delete()->where([['id', '=', 1]])->execute();
```

**Delete Multiple Records:**

```php
$post = new Post();
$post->delete()
    ->where([['status', '=', 'spam']])
    ->execute();
```

### Advanced Queries

#### WHERE Clauses

**Simple WHERE:**

```php
$post = new Post();
$results = $post->all()
    ->where([['status', '=', 'published']])
    ->getAll();
```

**Multiple Conditions with AND:**

```php
$post = new Post();
$results = $post->all()
    ->where([
        ['status', '=', 'published', 'AND'],
        ['author_id', '=', 5]
    ])
    ->getAll();
```

**Using Different Operators:**

```php
$post = new Post();
$results = $post->all()
    ->where([
        ['views', '>', 100, 'AND'],
        ['created_at', '>=', '2024-01-01']
    ])
    ->getAll();
```

#### ORDER BY

**Order Descending:**

```php
$post = new Post();
$results = $post->all()
    ->orderBy('created_at', 'DESC')
    ->getAll();
```

**Order Ascending:**

```php
$post = new Post();
$results = $post->all()
    ->orderBy('title', 'ASC')
    ->getAll();
```

#### LIMIT

**Limit Results:**

```php
$post = new Post();
$results = $post->all()
    ->limit(10)
    ->getAll();
```

**Pagination Example:**

```php
$post = new Post();
$page = 2;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Note: For offset, you may need to enhance the library
$results = $post->all()
    ->orderBy('created_at', 'DESC')
    ->limit($per_page)
    ->getAll();
```

#### Combining Multiple Clauses

```php
$post = new Post();
$results = $post->all()
    ->where([
        ['status', '=', 'published', 'AND'],
        ['author_id', '=', 5]
    ])
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->getAll();
```

### Aggregate Functions

#### COUNT

**Count all records:**

```php
$post = new Post();
$result = $post->select([], 'COUNT', '*')->get();
echo "Total posts: " . $result->{'COUNT(*)'};
```

**Count with GROUP BY:**

```php
$post = new Post();
$results = $post->select(['author_id'], 'COUNT', 'id')
    ->groupBy('author_id')
    ->getAll();

foreach ($results as $result) {
    echo "Author {$result->author_id} has {$result->{'COUNT(id)'}} posts<br>";
}
```

#### SUM, AVG, MIN, MAX

```php
// Sum of all views
$post = new Post();
$result = $post->select([], 'SUM', 'views')->get();

// Average views
$result = $post->select([], 'AVG', 'views')->get();

// Maximum views
$result = $post->select([], 'MAX', 'views')->get();

// Minimum views
$result = $post->select([], 'MIN', 'views')->get();
```

#### HAVING Clause

Use HAVING with aggregate functions:

```php
$post = new Post();
$results = $post->select(['author_id'], 'COUNT', 'id')
    ->groupBy('author_id')
    ->having([['COUNT(id)', '>', 5]])
    ->getAll();
```

## Advanced Features

### Automatic Timestamps

Enable automatic timestamp management for `created_at` and `updated_at` columns:

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];
    
    // Enable automatic timestamps (default: true)
    protected $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}

$post = new Post();
$post->columns['title'] = 'My Post';
$post->insert();
// created_at and updated_at are automatically set

// Update also automatically updates updated_at
$post->columns['title'] = 'Updated Title';
$post->update()->where([['id', '=', 1]])->execute();
// updated_at is automatically updated
```

### Soft Deletes

Soft delete marks records as deleted without removing them from the database:

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];
    
    // Enable soft deletes
    protected $soft_deletes = true;

    public function __construct()
    {
        parent::__construct();
    }
}

// Soft delete (sets deleted_at timestamp)
$post = new Post();
$post->delete()->where([['id', '=', 1]])->execute();

// Get only soft-deleted records
$trashed = $post->onlyTrashed()->getAll();

// Include soft-deleted in results
$all = $post->withTrashed()->all()->getAll();

// Restore a soft-deleted record
$post->restore()->where([['id', '=', 1]])->execute();

// Force delete (permanent)
$post->delete(true)->where([['id', '=', 1]])->execute();
```

### Custom Primary Keys

Override the default 'id' primary key:

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];
    
    // Custom primary key
    protected $primary_key = 'post_id';

    public function __construct()
    {
        parent::__construct();
    }
}

$post = new Post();
$result = $post->find(123); // Uses 'post_id' instead of 'id'
```

### Pagination with OFFSET

Full pagination support:

```php
$post = new Post();
$page = 2;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$results = $post->all()
    ->orderBy('created_at', 'DESC')
    ->limit($per_page)
    ->offset($offset)
    ->getAll();
```

### Batch Insert

Insert multiple records efficiently:

```php
$post = new Post();
$records = [
    ['title' => 'Post 1', 'body' => 'Content 1', 'author_id' => 1],
    ['title' => 'Post 2', 'body' => 'Content 2', 'author_id' => 2],
    ['title' => 'Post 3', 'body' => 'Content 3', 'author_id' => 1],
];

$result = $post->insertMany($records);
```

### Advanced WHERE Clauses

#### WHERE IN

```php
$post = new Post();
$results = $post->all()
    ->whereIn('id', [1, 2, 3, 5, 8])
    ->getAll();
```

#### WHERE NOT IN

```php
$post = new Post();
$results = $post->all()
    ->whereNotIn('status', ['draft', 'archived'])
    ->getAll();
```

#### WHERE BETWEEN

```php
$post = new Post();
$results = $post->all()
    ->whereBetween('views', 100, 1000)
    ->getAll();
```

#### WHERE NULL / WHERE NOT NULL

```php
$post = new Post();
$with_featured = $post->all()
    ->whereNotNull('featured_image')
    ->getAll();

$without_category = $post->all()
    ->whereNull('category_id')
    ->getAll();
```

### Query Scopes

Define reusable query constraints:

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
        
        // Define a 'published' scope
        $this->scope('published', function($query) {
            $query->where([['status', '=', 'published']]);
        });
        
        // Define a 'popular' scope with parameters
        $this->scope('popular', function($query, $min_views = 1000) {
            $query->where([['views', '>', $min_views]]);
        });
    }
}

// Use scopes
$post = new Post();
$published = $post->all()->applyScope('published')->getAll();
$popular = $post->all()->applyScope('popular', 500)->getAll();
```

### JOIN Operations

Perform table joins:

```php
// INNER JOIN
$post = new Post();
$results = $post->select(['posts.title', 'users.name'])
    ->join('users', 'posts.author_id', '=', 'users.id')
    ->getAll();

// LEFT JOIN
$post = new Post();
$results = $post->select(['posts.*', 'categories.name'])
    ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
    ->getAll();

// RIGHT JOIN
$post = new Post();
$results = $post->select(['posts.*', 'tags.name'])
    ->rightJoin('tags', 'posts.tag_id', '=', 'tags.id')
    ->getAll();
```

### Helper Methods

#### first()

Get the first record:

```php
$post = new Post();
$latest = $post->all()
    ->orderBy('created_at', 'DESC')
    ->first();
```

#### count()

Count records:

```php
$post = new Post();
$total = $post->all()->count();
$published_count = $post->all()->where([['status', '=', 'published']])->count();
```

#### exists()

Check if records exist:

```php
$post = new Post();
$has_posts = $post->all()->exists();
$has_published = $post->all()->where([['status', '=', 'published']])->exists();
```

#### pluck()

Get a single column's values:

```php
$post = new Post();
$titles = $post->all()->pluck('title');
// Returns: ['Title 1', 'Title 2', 'Title 3']
```

### Transaction Support

Manage database transactions:

```php
$post = new Post();

try {
    $post->beginTransaction();
    
    $post->columns['title'] = 'Post 1';
    $post->insert();
    
    $post->columns['title'] = 'Post 2';
    $post->insert();
    
    $post->commit();
    echo "Transaction committed";
} catch (Exception $e) {
    $post->rollback();
    echo "Transaction rolled back";
}
```

### Raw Queries

Execute custom SQL:

```php
$post = new Post();
$results = $post->raw(
    "SELECT status, COUNT(*) as count FROM posts WHERE created_at > :date GROUP BY status",
    ['date' => '2024-01-01']
);
```

### Chunking

Process large datasets efficiently:

```php
$post = new Post();
$post->all()->chunk(100, function($posts) {
    // Process 100 posts at a time
    foreach ($posts as $p) {
        // Process each post
    }
});
```

### Update or Create

Update existing record or create new one:

```php
$post = new Post();
$result = $post->updateOrCreate(
    ['slug' => 'my-unique-post'],
    ['title' => 'My Unique Post', 'body' => 'Content', 'status' => 'published']
);
```

### Distinct Results

Get unique records:

```php
$post = new Post();
$unique_authors = $post->select(['author_id'])->distinct()->getAll();
```

### Get Last Insert ID

```php
$post = new Post();
$post->columns['title'] = 'New Post';
$post->insert();
$last_id = $post->lastInsertId();
echo "New post ID: " . $last_id;
```

## API Reference

### Core Methods

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `all()` | None | `$this` | Selects all records from table |
| `select()` | `array $columns`, `string $aggregate`, `string $parameter` | `$this` | Select specific columns with optional aggregate |
| `find()` | `mixed $id` | `object\|null` | Find record by primary key |
| `insert()` | None | `bool` | Insert new record using $columns array |
| `update()` | None | `$this` | Update records (use with where()) |
| `delete()` | `bool $force` | `$this` | Delete records (supports soft delete) |
| `where()` | `array $conditions` | `$this` | Add WHERE conditions |
| `orderBy()` | `string $column`, `string $direction` | `$this` | Order results |
| `groupBy()` | `string $column` | `$this` | Group results |
| `having()` | `array $conditions` | `$this` | Add HAVING conditions |
| `limit()` | `int $number` | `$this` | Limit number of results |
| `offset()` | `int $number` | `$this` | Offset for pagination |
| `getAll()` | None | `array` | Execute query and fetch all results |
| `get()` | None | `object\|null` | Execute query and fetch single result |
| `execute()` | None | `bool` | Execute query (for UPDATE/DELETE) |

### Advanced Methods

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `withTrashed()` | None | `$this` | Include soft deleted records |
| `onlyTrashed()` | None | `$this` | Get only soft deleted records |
| `restore()` | None | `$this` | Restore soft deleted records |
| `insertMany()` | `array $records` | `bool` | Insert multiple records at once |
| `whereIn()` | `string $column`, `array $values` | `$this` | WHERE IN clause |
| `whereNotIn()` | `string $column`, `array $values` | `$this` | WHERE NOT IN clause |
| `whereBetween()` | `string $column`, `mixed $start`, `mixed $end` | `$this` | WHERE BETWEEN clause |
| `whereNull()` | `string $column` | `$this` | WHERE NULL clause |
| `whereNotNull()` | `string $column` | `$this` | WHERE NOT NULL clause |
| `first()` | None | `object\|null` | Get the first record |
| `count()` | None | `int` | Count records |
| `exists()` | None | `bool` | Check if records exist |
| `pluck()` | `string $column` | `array` | Get single column values |
| `scope()` | `string $name`, `callable $callback` | `void` | Define a query scope |
| `applyScope()` | `string $name`, `...$args` | `$this` | Apply a query scope |
| `beginTransaction()` | None | `bool` | Start a database transaction |
| `commit()` | None | `bool` | Commit a database transaction |
| `rollback()` | None | `bool` | Rollback a database transaction |
| `raw()` | `string $query`, `array $params` | `mixed` | Execute raw SQL query |
| `lastInsertId()` | None | `string` | Get last inserted ID |
| `chunk()` | `int $size`, `callable $callback` | `void` | Process results in chunks |
| `join()` | `string $table`, `string $first`, `string $op`, `string $second`, `string $type` | `$this` | Join tables |
| `leftJoin()` | `string $table`, `string $first`, `string $op`, `string $second` | `$this` | LEFT JOIN |
| `rightJoin()` | `string $table`, `string $first`, `string $op`, `string $second` | `$this` | RIGHT JOIN |
| `distinct()` | None | `$this` | Get distinct records |
| `updateOrCreate()` | `array $attributes`, `array $values` | `bool` | Update existing or create new record |

### Condition Array Format

For `where()` and `having()` methods:

```php
[
    ['column_name', 'operator', 'value', 'logical_operator'],
    ['column_name', 'operator', 'value']  // Last condition doesn't need logical operator
]
```

**Supported Operators:** `=`, `>`, `<`, `>=`, `<=`, `!=`, `<>`

**Logical Operators:** `AND`, `OR`

## Examples

See the `example.php` file for complete working examples. Here are some real-world scenarios:

### Blog Post Management

```php
<?php
require_once 'Post.php';

$post = new Post();

// Get all published posts ordered by date
$published_posts = $post->all()
    ->where([['status', '=', 'published']])
    ->orderBy('created_at', 'DESC')
    ->getAll();

// Get popular posts (views > 1000)
$popular_posts = $post->all()
    ->where([['views', '>', 1000]])
    ->orderBy('views', 'DESC')
    ->limit(5)
    ->getAll();

// Count posts by status
$post_counts = $post->select(['status'], 'COUNT', 'id')
    ->groupBy('status')
    ->getAll();
```

### User Management

```php
<?php
class User extends SoftMapper
{
    public $table_name = "users";
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
    }
}

$user = new User();

// Register new user
$user->columns['username'] = 'johndoe';
$user->columns['email'] = 'john@example.com';
$user->columns['password'] = password_hash('secret', PASSWORD_DEFAULT);
$user->columns['created_at'] = date('Y-m-d H:i:s');
$user->insert();

// Find user by email
$found_user = $user->all()
    ->where([['email', '=', 'john@example.com']])
    ->get();
```

## Security

### SQL Injection Protection

Soft-Mapper uses **PDO prepared statements** for all database queries, which automatically protects against SQL injection attacks. User input is never directly concatenated into SQL queries.

```php
// Safe from SQL injection
$post = new Post();
$user_input = $_GET['id']; // Even malicious input is safe
$result = $post->find($user_input);
```

### Best Practices

1. **Never expose database credentials**: Keep `env.php` outside your web root or use environment variables
2. **Validate user input**: Even though SQL injection is prevented, validate data types and formats
3. **Hash passwords**: Always use `password_hash()` for storing passwords
4. **Sanitize output**: Use `htmlspecialchars()` when displaying data in HTML
5. **Use HTTPS**: Encrypt data in transit
6. **Implement access controls**: Check user permissions before database operations

Example with validation:

```php
$post = new Post();

// Validate input
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);

if (strlen($title) > 0 && strlen($body) > 0) {
    $post->columns['title'] = $title;
    $post->columns['body'] = $body;
    $post->insert();
} else {
    echo "Invalid input";
}
```

## Troubleshooting

### Common Issues

**1. PDO Connection Error**

```
Error: SQLSTATE[HY000] [2002] No such file or directory
```

**Solution:** Check your database configuration in `env.php`. Ensure MySQL is running.

**2. Table doesn't exist**

```
Error: SQLSTATE[42S02]: Base table or view not found
```

**Solution:** Verify that `$table_name` in your model matches your actual database table name.

**3. Column not found**

```
Error: SQLSTATE[42S22]: Column not found
```

**Solution:** Ensure column names in `$columns` array match your database schema.

**4. Class 'PDO' not found**

**Solution:** Enable the PDO and PDO_MySQL extensions in your `php.ini`:

```ini
extension=pdo
extension=pdo_mysql
```

### Debug Mode

To see the generated SQL queries, you can temporarily add debugging to the SoftMapper class:

```php
// In getAll(), get(), or execute() methods, add:
var_dump($this->built_query);
var_dump($this->query_columns_place_holder_array);
```

### Getting Help

If you encounter issues:

1. Check the [examples](#examples) section
2. Review your database configuration
3. Verify your table and column names
4. Check PHP and MySQL versions meet [requirements](#requirements)
5. Open an issue on [GitHub](https://github.com/DedSecTeam17/Soft-Mapper/issues)

## Contributing

Contributions are welcome! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Make your changes**: Implement your feature or bug fix
4. **Test your changes**: Ensure everything works as expected
5. **Commit your changes**: `git commit -m 'Add amazing feature'`
6. **Push to the branch**: `git push origin feature/amazing-feature`
7. **Open a Pull Request**

### Development Guidelines

- Follow PSR coding standards
- Add comments for complex logic
- Update documentation for new features
- Test with multiple PHP versions if possible
- Keep backward compatibility in mind

## License

This project is open source and available under the MIT License. Feel free to use, modify, and distribute this software.

## Author

**Mohammed Elamin**

- Created: December 2018
- GitHub: [@DedSecTeam17](https://github.com/DedSecTeam17)

---

### Star this Repository ⭐

If you find Soft-Mapper useful, please consider giving it a star on GitHub. It helps others discover the project!

### Support

For questions, issues, or feature requests, please visit:
- [GitHub Issues](https://github.com/DedSecTeam17/Soft-Mapper/issues)
- [GitHub Discussions](https://github.com/DedSecTeam17/Soft-Mapper/discussions)

---

**Happy Coding! 🚀**

