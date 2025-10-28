# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-10-28

### Added - Major Feature Release 🎉

#### Core Enhancements
- **Automatic Timestamps**: Auto-manage `created_at` and `updated_at` columns (enabled by default)
- **Soft Deletes**: Mark records as deleted without removing from database
- **Custom Primary Keys**: Support for non-'id' primary key columns
- **Pagination OFFSET**: Full pagination support with `offset()` method

#### Query Builder Methods
- `offset($number)` - Add OFFSET clause for pagination
- `whereIn($column, $values)` - WHERE IN clause
- `whereNotIn($column, $values)` - WHERE NOT IN clause
- `whereBetween($column, $start, $end)` - WHERE BETWEEN clause
- `whereNull($column)` - WHERE NULL clause
- `whereNotNull($column)` - WHERE NOT NULL clause
- `distinct()` - Get distinct/unique records only

#### Helper Methods
- `first()` - Get the first record
- `count()` - Count records matching query
- `exists()` - Check if records exist
- `pluck($column)` - Extract single column values as array

#### Batch Operations
- `insertMany($records)` - Insert multiple records efficiently with transaction support

#### Soft Delete Methods
- `withTrashed()` - Include soft deleted records in results
- `onlyTrashed()` - Get only soft deleted records
- `restore()` - Restore soft deleted records
- `delete($force)` - Soft delete by default, force delete with `true` parameter

#### Transaction Support
- `beginTransaction()` - Start database transaction
- `commit()` - Commit transaction
- `rollback()` - Rollback transaction

#### Advanced Features
- `raw($query, $params)` - Execute raw SQL queries with parameter binding
- `lastInsertId()` - Get last inserted ID
- `chunk($size, $callback)` - Process large datasets in memory-efficient chunks
- `updateOrCreate($attributes, $values)` - Update existing or create new record
- `scope($name, $callback)` - Define reusable query scopes
- `applyScope($name, ...$args)` - Apply defined scopes to queries

#### JOIN Support
- `join($table, $first, $operator, $second, $type)` - Generic JOIN method
- `leftJoin($table, $first, $operator, $second)` - LEFT JOIN
- `rightJoin($table, $first, $operator, $second)` - RIGHT JOIN

#### Configuration Options
- `protected $primary_key` - Customize primary key column name (default: 'id')
- `protected $timestamps` - Enable/disable automatic timestamps (default: true)
- `protected $soft_deletes` - Enable/disable soft deletes (default: false)
- `protected $scopes` - Array to store custom query scopes

### Changed
- `delete()` now supports soft deletes when enabled
- `all()` now automatically excludes soft deleted records when soft deletes are enabled
- `find()` now uses custom primary key if defined

### Documentation
- Added comprehensive "Advanced Features" section to README
- Created `advanced-example.php` with real-world usage examples
- Updated API Reference with all new methods
- Added migration guide for upgrading from v1.x

### Testing
- Added `test-features.php` to verify all new features work correctly

### Backward Compatibility
- ✅ All changes are backward compatible with existing code
- ✅ Automatic timestamps are enabled by default but can be disabled
- ✅ Soft deletes are disabled by default
- ✅ Default primary key remains 'id'

## [1.0.0] - 2018-12-12

### Initial Release
- Basic CRUD operations (Create, Read, Update, Delete)
- Query builder with method chaining
- WHERE and HAVING clauses
- Aggregate functions (COUNT, SUM, AVG, MIN, MAX)
- ORDER BY and GROUP BY support
- LIMIT clause
- PDO-based with prepared statements for security
- Simple and intuitive API

---

## Upgrade Guide

### From v1.x to v2.0

**No breaking changes** - v2.0 is fully backward compatible!

#### New Features You Can Start Using:

1. **Enable Soft Deletes** (optional):
```php
class Post extends SoftMapper
{
    protected $soft_deletes = true;
}
```

2. **Disable Timestamps** (if you don't want them):
```php
class Post extends SoftMapper
{
    protected $timestamps = false;
}
```

3. **Custom Primary Key** (if not using 'id'):
```php
class Post extends SoftMapper
{
    protected $primary_key = 'post_id';
}
```

4. **Use New Helper Methods**:
```php
$count = $post->all()->count();
$exists = $post->all()->where([['status', '=', 'published']])->exists();
$first = $post->all()->orderBy('created_at', 'DESC')->first();
```

5. **Pagination with OFFSET**:
```php
$page = 2;
$per_page = 10;
$results = $post->all()
    ->limit($per_page)
    ->offset(($page - 1) * $per_page)
    ->getAll();
```

See `advanced-example.php` for comprehensive examples of all new features.
