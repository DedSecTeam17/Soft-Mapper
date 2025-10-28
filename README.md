# Soft-Mapper

A lightweight PHP ORM (Object-Relational Mapping) library for MySQL databases.

## Features

- Simple and intuitive query builder
- Method chaining for complex queries
- Support for CRUD operations (Create, Read, Update, Delete)
- Prepared statements for SQL injection protection
- Aggregate functions support (COUNT, SUM, AVG, etc.)
- Conditional queries with WHERE and HAVING clauses
- Ordering, grouping, and limiting results

## Installation

1. Clone this repository
2. Configure your database connection in `env.php`
3. Require the SoftMapper class in your models

## Configuration

Edit `env.php` to set your database credentials:

```php
define('host', 'localhost');
define('dbname', 'your_database_name');
define('user', 'your_username');
define('password', 'your_password');
```

## Usage

### Creating a Model

Extend the `SoftMapper` class to create your own models:

```php
require_once 'SoftMapper.php';

class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
    }
}
```

### Basic Operations

#### Insert

```php
$post = new Post();
$post->columns['title'] = 'My Post Title';
$post->columns['body'] = 'Post content here';
$post->insert();
```

#### Find by ID

```php
$post = new Post();
$result = $post->find(1);
```

#### Get All Records

```php
$post = new Post();
$results = $post->all()->getAll();
```

#### Update

```php
$post = new Post();
$post->columns['title'] = 'Updated Title';
$post->update()->where([['id', '=', 1]])->execute();
```

#### Delete

```php
$post = new Post();
$post->delete()->where([['id', '=', 1]])->execute();
```

### Advanced Queries

#### Select Specific Columns

```php
$post = new Post();
$results = $post->select(['title', 'created_at'])->getAll();
```

#### Where Clause

```php
$post = new Post();
$results = $post->all()
    ->where([
        ['status', '=', 'published', 'AND'],
        ['author_id', '=', 5]
    ])
    ->getAll();
```

#### Order By

```php
$post = new Post();
$results = $post->all()->orderBy('created_at', 'DESC')->getAll();
```

#### Limit

```php
$post = new Post();
$results = $post->all()->limit(10)->getAll();
```

#### Group By with Aggregate Functions

```php
$post = new Post();
$results = $post->select(['author_id'], 'COUNT', 'id')
    ->groupBy('author_id')
    ->getAll();
```

## Example

See `example.php` for complete usage examples.

## License

Open source - feel free to use and modify.

