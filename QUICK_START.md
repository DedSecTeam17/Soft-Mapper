# Quick Start Guide - Advanced Features

This guide introduces the new advanced features added to Soft-Mapper v2.0.

## Table of Contents
1. [Automatic Timestamps](#automatic-timestamps)
2. [Soft Deletes](#soft-deletes)
3. [Pagination](#pagination)
4. [Query Scopes](#query-scopes)
5. [Batch Operations](#batch-operations)
6. [Transactions](#transactions)
7. [Advanced Queries](#advanced-queries)

## Automatic Timestamps

Automatically manage `created_at` and `updated_at` columns.

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];
    
    // Enabled by default
    protected $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}

// Usage
$post = new Post();
$post->columns['title'] = 'My Post';
$post->insert();
// created_at and updated_at are set automatically!

// Updates also handle timestamps
$post->columns['title'] = 'Updated Title';
$post->update()->where([['id', '=', 1]])->execute();
// updated_at is updated automatically!
```

**To disable timestamps:**
```php
protected $timestamps = false;
```

## Soft Deletes

Mark records as deleted without actually removing them from the database.

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
$post->delete()->where([['id', '=', 1]])->execute();

// Regular queries automatically exclude soft-deleted records
$active_posts = $post->all()->getAll();

// Include soft-deleted records
$all_posts = $post->withTrashed()->all()->getAll();

// Get only soft-deleted records
$trashed_posts = $post->onlyTrashed()->getAll();

// Restore a soft-deleted record
$post->restore()->where([['id', '=', 1]])->execute();

// Force delete (permanent deletion)
$post->delete(true)->where([['id', '=', 1]])->execute();
```

## Pagination

Complete pagination support with `offset()`.

```php
$post = new Post();

// Page 1
$page1 = $post->all()
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(0)
    ->getAll();

// Page 2
$page2 = $post->all()
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(10)
    ->getAll();

// Or use a pagination helper
function paginate($model, $page, $per_page) {
    return $model->all()
        ->orderBy('created_at', 'DESC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page)
        ->getAll();
}

$results = paginate($post, 2, 10);
```

## Query Scopes

Define reusable query constraints.

```php
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
        
        // Define scopes
        $this->scope('published', function($query) {
            $query->where([['status', '=', 'published']]);
        });
        
        $this->scope('byAuthor', function($query, $author_id) {
            $query->where([['author_id', '=', $author_id]]);
        });
        
        $this->scope('popular', function($query, $min_views = 1000) {
            $query->where([['views', '>', $min_views]]);
        });
    }
}

// Use scopes
$post = new Post();

// Get all published posts
$published = $post->all()->applyScope('published')->getAll();

// Get posts by specific author
$author_posts = $post->all()->applyScope('byAuthor', 5)->getAll();

// Get popular posts
$popular = $post->all()->applyScope('popular', 500)->getAll();

// Chain multiple scopes
$results = $post->all()
    ->applyScope('published')
    ->applyScope('popular', 1000)
    ->orderBy('created_at', 'DESC')
    ->getAll();
```

## Batch Operations

Efficiently insert multiple records.

```php
$post = new Post();

$articles = [
    ['title' => 'Article 1', 'body' => 'Content 1', 'author_id' => 1],
    ['title' => 'Article 2', 'body' => 'Content 2', 'author_id' => 2],
    ['title' => 'Article 3', 'body' => 'Content 3', 'author_id' => 1],
];

// Insert all at once with transaction
if ($post->insertMany($articles)) {
    echo "All articles inserted successfully!";
}
```

## Transactions

Ensure data consistency with transactions.

```php
$post = new Post();

try {
    $post->beginTransaction();
    
    // Insert first post
    $post->columns['title'] = 'Post 1';
    $post->columns['body'] = 'Content 1';
    $post->insert();
    
    // Insert second post
    $post->columns['title'] = 'Post 2';
    $post->columns['body'] = 'Content 2';
    $post->insert();
    
    // Update related record
    $post->columns['status'] = 'published';
    $post->update()->where([['id', '=', 1]])->execute();
    
    $post->commit();
    echo "All operations completed successfully!";
    
} catch (Exception $e) {
    $post->rollback();
    echo "Error: Transaction rolled back";
}
```

## Advanced Queries

### WHERE IN / NOT IN

```php
$post = new Post();

// Get posts with specific IDs
$posts = $post->all()
    ->whereIn('id', [1, 2, 3, 5, 8])
    ->getAll();

// Exclude certain statuses
$posts = $post->all()
    ->whereNotIn('status', ['draft', 'archived'])
    ->getAll();
```

### WHERE BETWEEN

```php
$post = new Post();

// Get posts with views between 100 and 1000
$posts = $post->all()
    ->whereBetween('views', 100, 1000)
    ->getAll();
```

### WHERE NULL / NOT NULL

```php
$post = new Post();

// Get posts without category
$uncategorized = $post->all()
    ->whereNull('category_id')
    ->getAll();

// Get posts with featured image
$featured = $post->all()
    ->whereNotNull('featured_image')
    ->getAll();
```

### Helper Methods

```php
$post = new Post();

// Count records
$total = $post->all()->count();

// Check if records exist
$has_published = $post->all()
    ->where([['status', '=', 'published']])
    ->exists();

// Get first record
$latest = $post->all()
    ->orderBy('created_at', 'DESC')
    ->first();

// Pluck specific column
$titles = $post->all()->pluck('title');
// Returns: ['Title 1', 'Title 2', 'Title 3']
```

### JOINs

```php
$post = new Post();

// INNER JOIN
$posts_with_authors = $post->select(['posts.title', 'users.name'])
    ->join('users', 'posts.author_id', '=', 'users.id')
    ->getAll();

// LEFT JOIN
$posts_with_categories = $post->select(['posts.*', 'categories.name'])
    ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
    ->getAll();
```

### Chunking (Memory Efficient)

```php
$post = new Post();

// Process 100 records at a time
$post->all()->chunk(100, function($posts) {
    foreach ($posts as $post) {
        // Process each post
        echo "Processing: " . $post->title . "\n";
    }
});
```

### Update or Create

```php
$post = new Post();

// Update if exists, create if not
$post->updateOrCreate(
    ['slug' => 'my-unique-post'],  // Search criteria
    ['title' => 'My Post', 'body' => 'Content']  // Values to set
);
```

### Raw Queries

```php
$post = new Post();

// Execute custom SQL
$results = $post->raw(
    "SELECT status, COUNT(*) as count 
     FROM posts 
     WHERE created_at > :date 
     GROUP BY status",
    ['date' => '2024-01-01']
);
```

## Complete Example

```php
<?php
require_once 'SoftMapper.php';

class Article extends SoftMapper
{
    public $table_name = "articles";
    public $columns = [];
    
    protected $timestamps = true;
    protected $soft_deletes = true;

    public function __construct()
    {
        parent::__construct();
        
        $this->scope('published', function($query) {
            $query->where([['status', '=', 'published']]);
        });
        
        $this->scope('recent', function($query, $days = 7) {
            $date = date('Y-m-d', strtotime("-{$days} days"));
            $query->where([['created_at', '>', $date]]);
        });
    }
}

// Create article
$article = new Article();
$article->columns['title'] = 'Getting Started with PHP ORM';
$article->columns['body'] = 'This is a comprehensive guide...';
$article->columns['status'] = 'published';
$article->insert();

// Get recent published articles with pagination
$article = new Article();
$results = $article->all()
    ->applyScope('published')
    ->applyScope('recent', 30)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(0)
    ->getAll();

// Count published articles
$count = $article->all()->applyScope('published')->count();
echo "Total published articles: {$count}\n";
```

## Next Steps

- See `advanced-example.php` for more comprehensive examples
- Read the full [README.md](README.md) for detailed documentation
- Check [CHANGELOG.md](CHANGELOG.md) for all new features

Happy coding! 🚀
