# ORM Relationships Documentation

This guide explains how to use the relationship features in Soft-Mapper to work with related data across multiple database tables.

## Table of Contents

- [Introduction](#introduction)
- [Relationship Types](#relationship-types)
- [Defining Relationships](#defining-relationships)
- [Loading Relationships](#loading-relationships)
- [Eager Loading](#eager-loading)
- [Many-to-Many Operations](#many-to-many-operations)
- [Complete Examples](#complete-examples)

## Introduction

Soft-Mapper now supports ORM relationships, allowing you to define and work with related data using simple, object-oriented PHP code. You can define relationships between models and load related data without writing complex SQL queries.

### Supported Relationship Types

- **One-to-One (1:1)**: `hasOne()` and `belongsTo()`
- **One-to-Many (1:N)**: `hasMany()` and `belongsTo()`
- **Many-to-Many (N:N)**: `belongsToMany()`

## Relationship Types

### One-to-One (1:1)

A one-to-one relationship is when one record in a table is associated with exactly one record in another table.

**Example**: A User has one Profile.

```php
class User extends SoftMapper
{
    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'id');
    }
}

class UserProfile extends SoftMapper
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}
```

### One-to-Many (1:N)

A one-to-many relationship is when one record can be associated with multiple records in another table.

**Example**: A User has many Posts.

```php
class User extends SoftMapper
{
    public function posts()
    {
        return $this->hasMany('Post', 'user_id', 'id');
    }
}

class Post extends SoftMapper
{
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }
}
```

### Many-to-Many (N:N)

A many-to-many relationship is when multiple records can be associated with multiple records in another table through a pivot/junction table.

**Example**: Posts and Tags (a post can have many tags, and a tag can belong to many posts).

```php
class Post extends SoftMapper
{
    public function tags()
    {
        return $this->belongsToMany('Tag', 'post_tag', 'post_id', 'tag_id');
    }
}

class Tag extends SoftMapper
{
    public function posts()
    {
        return $this->belongsToMany('Post', 'post_tag', 'tag_id', 'post_id');
    }
}
```

## Defining Relationships

### hasOne()

Defines a one-to-one relationship where the foreign key is on the related table.

```php
public function hasOne($related_class, $foreign_key = null, $local_key = null)
```

**Parameters**:
- `$related_class`: Name of the related model class
- `$foreign_key`: Foreign key on the related table (default: `{this_table}_id`)
- `$local_key`: Local key on this table (default: primary key)

**Example**:
```php
class User extends SoftMapper
{
    public $table_name = "users";
    
    public function profile()
    {
        // User has one profile
        // Looks for 'user_id' in 'user_profiles' table
        return $this->hasOne('UserProfile');
    }
}
```

### hasMany()

Defines a one-to-many relationship where the foreign key is on the related table.

```php
public function hasMany($related_class, $foreign_key = null, $local_key = null)
```

**Parameters**:
- `$related_class`: Name of the related model class
- `$foreign_key`: Foreign key on the related table (default: `{this_table}_id`)
- `$local_key`: Local key on this table (default: primary key)

**Example**:
```php
class User extends SoftMapper
{
    public $table_name = "users";
    
    public function posts()
    {
        // User has many posts
        // Looks for 'user_id' in 'posts' table
        return $this->hasMany('Post');
    }
    
    public function comments()
    {
        // User has many comments
        return $this->hasMany('Comment', 'user_id', 'id');
    }
}
```

### belongsTo()

Defines the inverse of a one-to-one or one-to-many relationship.

```php
public function belongsTo($related_class, $foreign_key = null, $owner_key = null)
```

**Parameters**:
- `$related_class`: Name of the related model class
- `$foreign_key`: Foreign key on this table (default: `{related_table}_id`)
- `$owner_key`: Primary key on the related table (default: `id`)

**Example**:
```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    
    public function user()
    {
        // Post belongs to a user
        // Looks for 'user_id' on this table
        return $this->belongsTo('User');
    }
}
```

### belongsToMany()

Defines a many-to-many relationship with a pivot table.

```php
public function belongsToMany($related_class, $pivot_table = null, 
                             $foreign_pivot_key = null, $related_pivot_key = null,
                             $parent_key = null, $related_key = null)
```

**Parameters**:
- `$related_class`: Name of the related model class
- `$pivot_table`: Name of the pivot table (default: alphabetically ordered table names)
- `$foreign_pivot_key`: Foreign key for this model in pivot table (default: `{this_table}_id`)
- `$related_pivot_key`: Foreign key for related model in pivot table (default: `{related_table}_id`)
- `$parent_key`: Primary key on this table (default: primary key)
- `$related_key`: Primary key on related table (default: primary key)

**Example**:
```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    
    public function tags()
    {
        // Post belongs to many tags through 'post_tag' pivot table
        return $this->belongsToMany('Tag', 'post_tag', 'post_id', 'tag_id');
    }
}

class Tag extends SoftMapper
{
    public $table_name = "tags";
    
    public function posts()
    {
        // Tag belongs to many posts
        return $this->belongsToMany('Post', 'post_tag', 'tag_id', 'post_id');
    }
}
```

## Loading Relationships

### loadRelation()

Load a relationship for a single model instance.

```php
public function loadRelation($relation_name, $record)
```

**Example**:
```php
$user = new User();
$user_record = $user->find(1);

// Load the posts relationship
$user->loadRelation('posts', $user_record);

// Now you can access posts
foreach ($user_record->posts as $post) {
    echo $post->title . "\n";
}
```

### Loading Multiple Relationships

You can load multiple relationships by calling `loadRelation()` multiple times:

```php
$post = new Post();
$post_record = $post->find(1);

// Load user (author)
$post->loadRelation('user', $post_record);

// Load comments
$post->loadRelation('comments', $post_record);

// Load tags
$post->loadRelation('tags', $post_record);

// Access related data
echo "Post: " . $post_record->title . "\n";
echo "Author: " . $post_record->user->name . "\n";
echo "Comments: " . count($post_record->comments) . "\n";
echo "Tags: " . count($post_record->tags) . "\n";
```

## Eager Loading

Eager loading allows you to load relationships for multiple records efficiently, preventing N+1 query problems.

### with()

Specify relationships to eager load.

```php
public function with($relations = [])
```

**Example**:
```php
$post = new Post();

// Load all posts with their users and comments
$posts = $post->with(['user', 'comments'])->all()->getAll();

foreach ($posts as $p) {
    echo "Post: " . $p->title . "\n";
    echo "Author: " . $p->user->name . "\n";
    echo "Comments: " . count($p->comments) . "\n\n";
}
```

**Benefits of Eager Loading**:
- Reduces database queries
- Improves performance when working with multiple records
- Prevents N+1 query problems

## Many-to-Many Operations

For many-to-many relationships, Soft-Mapper provides convenient methods to manage the pivot table.

### attach()

Attach a related model to the parent model.

```php
public function attach($id, $related_id, $relation_name, $pivot_data = [])
```

**Example**:
```php
$post = new Post();

// Attach tag ID 5 to post ID 1
$post->attach(1, 5, 'tags');

// Attach with additional pivot data
$post->attach(1, 6, 'tags', ['order' => 1]);
```

### detach()

Detach a related model from the parent model.

```php
public function detach($id, $relation_name, $related_id = null)
```

**Example**:
```php
$post = new Post();

// Detach specific tag (ID 5) from post ID 1
$post->detach(1, 'tags', 5);

// Detach all tags from post ID 1
$post->detach(1, 'tags');
```

### sync()

Synchronize the relationship (detach all existing and attach new ones).

```php
public function sync($id, $related_ids, $relation_name)
```

**Example**:
```php
$post = new Post();

// Replace all existing tags with new ones (IDs: 1, 2, 3)
$post->sync(1, [1, 2, 3], 'tags');
```

## Complete Examples

### Example 1: Blog System

```php
// Define models
class User extends SoftMapper
{
    public $table_name = "users";
    
    public function posts()
    {
        return $this->hasMany('Post');
    }
    
    public function comments()
    {
        return $this->hasMany('Comment');
    }
}

class Post extends SoftMapper
{
    public $table_name = "posts";
    protected $soft_deletes = true;
    
    public function user()
    {
        return $this->belongsTo('User');
    }
    
    public function comments()
    {
        return $this->hasMany('Comment');
    }
    
    public function tags()
    {
        return $this->belongsToMany('Tag', 'post_tag');
    }
}

class Comment extends SoftMapper
{
    public $table_name = "comments";
    
    public function post()
    {
        return $this->belongsTo('Post');
    }
    
    public function user()
    {
        return $this->belongsTo('User');
    }
}

class Tag extends SoftMapper
{
    public $table_name = "tags";
    
    public function posts()
    {
        return $this->belongsToMany('Post', 'post_tag');
    }
}

// Usage examples

// 1. Create a new post
$post = new Post();
$post->columns = [
    'user_id' => 1,
    'title' => 'My First Post',
    'body' => 'This is my first blog post!',
    'status' => 'published'
];
$post->insert();
$post_id = $post->lastInsertId();

// 2. Add tags to the post
$post->attach($post_id, 1, 'tags'); // Attach "PHP" tag
$post->attach($post_id, 2, 'tags'); // Attach "MySQL" tag

// 3. Get post with all related data
$post = new Post();
$post_record = $post->find($post_id);
$post->loadRelation('user', $post_record);
$post->loadRelation('comments', $post_record);
$post->loadRelation('tags', $post_record);

echo "Post: " . $post_record->title . "\n";
echo "Author: " . $post_record->user->name . "\n";
echo "Comments: " . count($post_record->comments) . "\n";
echo "Tags: ";
foreach ($post_record->tags as $tag) {
    echo $tag->name . " ";
}
echo "\n";

// 4. Get all posts with eager loading
$post = new Post();
$posts = $post->with(['user', 'comments', 'tags'])
    ->all()
    ->where([['status', '=', 'published']])
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->getAll();

foreach ($posts as $p) {
    echo $p->title . " by " . $p->user->name . "\n";
}
```

### Example 2: E-commerce System

```php
class Product extends SoftMapper
{
    public $table_name = "products";
    
    public function category()
    {
        return $this->belongsTo('Category');
    }
    
    public function reviews()
    {
        return $this->hasMany('Review');
    }
    
    public function tags()
    {
        return $this->belongsToMany('Tag', 'product_tag');
    }
}

// Get product with all related data
$product = new Product();
$product_record = $product->find(1);
$product->loadRelation('category', $product_record);
$product->loadRelation('reviews', $product_record);
$product->loadRelation('tags', $product_record);

echo "Product: " . $product_record->name . "\n";
echo "Category: " . $product_record->category->name . "\n";
echo "Average Rating: " . calculateAverage($product_record->reviews) . "\n";
echo "Tags: " . implode(', ', array_map(function($t) { 
    return $t->name; 
}, $product_record->tags)) . "\n";
```

## Database Schema

Here's a complete database schema for the blog example:

```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    status VARCHAR(50) DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tags table
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pivot table for posts and tags (N:N relationship)
CREATE TABLE post_tag (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

## Best Practices

1. **Define relationships in model constructors or separate methods**: Keep your relationship definitions organized.

2. **Use eager loading for multiple records**: When fetching multiple records, use `with()` to avoid N+1 queries.

3. **Use lazy loading for single records**: When fetching a single record, use `loadRelation()` as needed.

4. **Follow naming conventions**: Use standard foreign key naming (`{table}_id`) for automatic relationship detection.

5. **Use transactions for complex operations**: When creating or updating multiple related records, wrap them in transactions.

```php
$post = new Post();
try {
    $post->beginTransaction();
    
    // Create post
    $post->columns = ['title' => 'New Post', 'user_id' => 1];
    $post->insert();
    $post_id = $post->lastInsertId();
    
    // Attach tags
    $post->attach($post_id, 1, 'tags');
    $post->attach($post_id, 2, 'tags');
    
    $post->commit();
} catch (Exception $e) {
    $post->rollback();
}
```

## Summary

Soft-Mapper's relationship features allow you to:
- Define relationships using simple OOP methods
- Load related data without complex SQL
- Use eager loading to optimize performance
- Manage many-to-many relationships easily
- Build complex queries with related data

This makes working with relational data in PHP much simpler and more intuitive!
