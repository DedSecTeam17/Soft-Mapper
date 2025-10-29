# Quick Start: ORM Relationships

This guide will get you started with ORM relationships in Soft-Mapper in just a few minutes.

## What are ORM Relationships?

ORM relationships allow you to define connections between database tables using simple PHP code. Instead of writing complex SQL JOINs, you define relationships in your models and Soft-Mapper handles the queries for you.

## Relationship Types

Soft-Mapper supports three types of relationships:

1. **One-to-One (1:1)**: e.g., A user has one profile
2. **One-to-Many (1:N)**: e.g., A user has many posts
3. **Many-to-Many (N:N)**: e.g., A post has many tags, and a tag has many posts

## 5-Minute Tutorial

### Step 1: Define Your Models

```php
<?php
require_once 'SoftMapper.php';

// User model
class User extends SoftMapper
{
    public $table_name = "users";
    public $columns = [];
    
    public function posts()
    {
        return $this->hasMany('Post');  // One user has many posts
    }
}

// Post model
class Post extends SoftMapper
{
    public $table_name = "posts";
    public $columns = [];
    
    public function user()
    {
        return $this->belongsTo('User');  // Each post belongs to one user
    }
    
    public function tags()
    {
        return $this->belongsToMany('Tag', 'post_tag');  // Many-to-many
    }
}

// Tag model
class Tag extends SoftMapper
{
    public $table_name = "tags";
    public $columns = [];
    
    public function posts()
    {
        return $this->belongsToMany('Post', 'post_tag');  // Many-to-many
    }
}
```

### Step 2: Create Your Database Tables

```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL
);

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tags table
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Pivot table for posts and tags
CREATE TABLE post_tag (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

### Step 3: Use Relationships

**Load a single relationship:**

```php
$user = new User();
$user_record = $user->find(1);

// Load the user's posts
$user->loadRelation('posts', $user_record);

// Access the posts
foreach ($user_record->posts as $post) {
    echo $post->title . "\n";
}
```

**Load multiple relationships efficiently (Eager Loading):**

```php
$post = new Post();

// Load all posts with their authors and tags
$posts = $post->with(['user', 'tags'])->all()->getAll();

foreach ($posts as $p) {
    echo "Post: " . $p->title . "\n";
    echo "Author: " . $p->user->name . "\n";
    echo "Tags: " . implode(', ', array_map(function($t) { 
        return $t->name; 
    }, $p->tags)) . "\n\n";
}
```

**Manage many-to-many relationships:**

```php
$post = new Post();

// Attach a tag to a post
$post->attach(1, 5, 'tags');  // Attach tag ID 5 to post ID 1

// Sync tags (replace all with new ones)
$post->sync(1, [1, 2, 3], 'tags');  // Post 1 now has only tags 1, 2, 3

// Detach a tag
$post->detach(1, 'tags', 5);  // Remove tag ID 5 from post ID 1
```

## Common Patterns

### Blog System

```php
// Get all posts by a specific user
$user = new User();
$user_record = $user->find(1);
$user->loadRelation('posts', $user_record);

foreach ($user_record->posts as $post) {
    echo $post->title . "\n";
}

// Get a post with its author and all comments
$post = new Post();
$post_record = $post->find(1);
$post->loadRelation('user', $post_record);
$post->loadRelation('comments', $post_record);

echo "Post: " . $post_record->title . "\n";
echo "Author: " . $post_record->user->name . "\n";
echo "Comments: " . count($post_record->comments) . "\n";
```

### E-commerce System

```php
// Get all products in a category
$category = new Category();
$category_record = $category->find(1);
$category->loadRelation('products', $category_record);

// Get a product with reviews
$product = new Product();
$product_record = $product->find(1);
$product->loadRelation('reviews', $product_record);

$avg_rating = array_sum(array_map(function($r) { 
    return $r->rating; 
}, $product_record->reviews)) / count($product_record->reviews);
```

## Performance Tips

### 1. Use Eager Loading for Multiple Records

**Bad (N+1 Problem):**
```php
$posts = $post->all()->getAll();
foreach ($posts as $p) {
    $post->loadRelation('user', $p);  // Separate query for each post!
}
```

**Good (Single Query):**
```php
$posts = $post->with(['user'])->all()->getAll();
foreach ($posts as $p) {
    echo $p->user->name;  // Already loaded!
}
```

### 2. Use Lazy Loading for Single Records

```php
$user = new User();
$user_record = $user->find(1);

// Only load what you need
$user->loadRelation('posts', $user_record);
```

### 3. Combine with Query Builder

```php
// Get published posts with their authors
$post = new Post();
$posts = $post->with(['user'])
    ->all()
    ->where([['status', '=', 'published']])
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->getAll();
```

## Next Steps

- Read the complete [RELATIONSHIPS.md](RELATIONSHIPS.md) documentation
- Check out [examples/relationships-example.php](examples/relationships-example.php) for more examples
- Look at the example models in the `examples/` directory

## Troubleshooting

**Q: My relationships aren't loading**
A: Make sure:
- Foreign keys are set correctly in the database
- You're calling the relationship method (e.g., `posts()`) not trying to access a property
- The related model class exists and is loaded

**Q: How do I know which method to use?**
A: 
- `hasOne()`: The foreign key is in the OTHER table (1:1)
- `hasMany()`: The foreign key is in the OTHER table (1:N)
- `belongsTo()`: The foreign key is in THIS table
- `belongsToMany()`: Use a pivot table for many-to-many

**Q: Can I customize foreign key names?**
A: Yes! All relationship methods accept optional parameters:
```php
$this->hasMany('Post', 'author_id', 'id');  // Custom foreign key
$this->belongsToMany('Tag', 'article_tag', 'article_id', 'tag_id');
```

Happy coding! 🚀
