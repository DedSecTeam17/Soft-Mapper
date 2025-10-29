<?php
    /**
     * Advanced Examples for SoftMapper Library
     * Demonstrates new features and capabilities
     */
    require_once 'SoftMapper.php';

    // Example Model with Timestamps and Soft Deletes
    class Article extends SoftMapper
    {
        public $table_name = "articles";
        public $columns = [];
        
        // Enable automatic timestamp management
        protected $timestamps = true;
        
        // Enable soft delete functionality
        protected $soft_deletes = true;
        
        // Custom primary key (if not 'id')
        // protected $primary_key = 'article_id';

        public function __construct()
        {
            parent::__construct();
            
            // Define custom scopes
            $this->scope('published', function($query) {
                $query->where([['status', '=', 'published']]);
            });
            
            $this->scope('popular', function($query, $min_views = 1000) {
                $query->where([['views', '>', $min_views]]);
            });
        }
    }

    echo "=== SoftMapper Advanced Features Demo ===\n\n";

    // ============================================
    // 1. AUTOMATIC TIMESTAMPS
    // ============================================
    echo "1. Automatic Timestamps:\n";
    $article = new Article();
    $article->columns['title'] = 'Advanced PHP ORM';
    $article->columns['content'] = 'This article demonstrates advanced ORM features.';
    $article->columns['status'] = 'published';
    $article->columns['views'] = 1500;
    // Note: created_at and updated_at are added automatically
    $article->insert();
    echo "✓ Article inserted with automatic timestamps\n";
    echo "✓ Last inserted ID: " . $article->lastInsertId() . "\n\n";

    // ============================================
    // 2. PAGINATION WITH OFFSET
    // ============================================
    echo "2. Pagination with Offset:\n";
    $article = new Article();
    $page = 2;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    $paginated_articles = $article->all()
        ->orderBy('created_at', 'DESC')
        ->limit($per_page)
        ->offset($offset)
        ->getAll();
    echo "✓ Fetched page {$page} with {$per_page} articles per page\n\n";

    // ============================================
    // 3. BATCH INSERT
    // ============================================
    echo "3. Batch Insert:\n";
    $article = new Article();
    $records = [
        ['title' => 'Article 1', 'content' => 'Content 1', 'status' => 'published', 'views' => 100],
        ['title' => 'Article 2', 'content' => 'Content 2', 'status' => 'draft', 'views' => 50],
        ['title' => 'Article 3', 'content' => 'Content 3', 'status' => 'published', 'views' => 2000],
    ];
    $result = $article->insertMany($records);
    echo $result ? "✓ Inserted multiple articles successfully\n\n" : "✗ Failed to insert articles\n\n";

    // ============================================
    // 4. QUERY SCOPES
    // ============================================
    echo "4. Query Scopes:\n";
    $article = new Article();
    $published_articles = $article->all()
        ->applyScope('published')
        ->getAll();
    echo "✓ Applied 'published' scope\n";

    $popular_articles = $article->all()
        ->applyScope('popular', 1000)
        ->getAll();
    echo "✓ Applied 'popular' scope with min_views = 1000\n\n";

    // ============================================
    // 5. WHERE IN / WHERE NOT IN
    // ============================================
    echo "5. WHERE IN / WHERE NOT IN:\n";
    $article = new Article();
    $specific_articles = $article->all()
        ->whereIn('id', [1, 2, 3, 5, 8])
        ->getAll();
    echo "✓ Fetched articles with IDs in [1, 2, 3, 5, 8]\n";

    $article = new Article();
    $excluded_articles = $article->all()
        ->whereNotIn('status', ['draft', 'archived'])
        ->getAll();
    echo "✓ Fetched articles excluding 'draft' and 'archived' status\n\n";

    // ============================================
    // 6. WHERE BETWEEN
    // ============================================
    echo "6. WHERE BETWEEN:\n";
    $article = new Article();
    $view_range_articles = $article->all()
        ->whereBetween('views', 100, 1000)
        ->getAll();
    echo "✓ Fetched articles with views between 100 and 1000\n\n";

    // ============================================
    // 7. WHERE NULL / WHERE NOT NULL
    // ============================================
    echo "7. WHERE NULL / WHERE NOT NULL:\n";
    $article = new Article();
    $archived_articles = $article->all()
        ->whereNotNull('archived_at')
        ->getAll();
    echo "✓ Fetched articles that are archived (archived_at IS NOT NULL)\n\n";

    // ============================================
    // 8. SOFT DELETES
    // ============================================
    echo "8. Soft Deletes:\n";
    $article = new Article();
    // Soft delete (sets deleted_at timestamp)
    $article->delete()->where([['id', '=', 1]])->execute();
    echo "✓ Soft deleted article with ID 1\n";

    // Get only trashed records
    $article = new Article();
    $trashed_articles = $article->onlyTrashed()->getAll();
    echo "✓ Fetched only soft-deleted articles: " . count($trashed_articles) . " found\n";

    // Include trashed in results
    $article = new Article();
    $all_articles = $article->withTrashed()->all()->getAll();
    echo "✓ Fetched all articles including soft-deleted\n";

    // Restore soft deleted record
    $article = new Article();
    $article->restore()->where([['id', '=', 1]])->execute();
    echo "✓ Restored article with ID 1\n";

    // Force delete (permanent)
    $article = new Article();
    $article->delete(true)->where([['id', '=', 1]])->execute();
    echo "✓ Force deleted article with ID 1 (permanent)\n\n";

    // ============================================
    // 9. HELPER METHODS
    // ============================================
    echo "9. Helper Methods:\n";

    // Count
    $article = new Article();
    $total = $article->all()->count();
    echo "✓ Total articles: {$total}\n";

    // First
    $article = new Article();
    $first_article = $article->all()->orderBy('created_at', 'DESC')->first();
    echo "✓ Latest article: " . ($first_article ? $first_article->title : 'None') . "\n";

    // Exists
    $article = new Article();
    $has_published = $article->all()->where([['status', '=', 'published']])->exists();
    echo $has_published ? "✓ Published articles exist\n" : "✓ No published articles\n";

    // Pluck
    $article = new Article();
    $titles = $article->all()->pluck('title');
    echo "✓ Plucked all article titles: " . count($titles) . " titles\n\n";

    // ============================================
    // 10. TRANSACTIONS
    // ============================================
    echo "10. Transactions:\n";
    $article = new Article();
    
    try {
        $article->beginTransaction();
        
        $article->columns['title'] = 'Transaction Test 1';
        $article->columns['content'] = 'Content 1';
        $article->insert();
        
        $article->columns['title'] = 'Transaction Test 2';
        $article->columns['content'] = 'Content 2';
        $article->insert();
        
        $article->commit();
        echo "✓ Transaction committed successfully\n\n";
    } catch (Exception $e) {
        $article->rollback();
        echo "✗ Transaction rolled back\n\n";
    }

    // ============================================
    // 11. JOINS
    // ============================================
    echo "11. JOIN Operations:\n";
    $article = new Article();
    $articles_with_authors = $article->select(['articles.title', 'users.name'])
        ->join('users', 'articles.author_id', '=', 'users.id')
        ->getAll();
    echo "✓ Performed INNER JOIN with users table\n";

    $article = new Article();
    $articles_left_join = $article->select(['articles.*', 'categories.name'])
        ->leftJoin('categories', 'articles.category_id', '=', 'categories.id')
        ->getAll();
    echo "✓ Performed LEFT JOIN with categories table\n\n";

    // ============================================
    // 12. DISTINCT
    // ============================================
    echo "12. DISTINCT:\n";
    $article = new Article();
    $unique_statuses = $article->select(['status'])->distinct()->getAll();
    echo "✓ Fetched distinct article statuses\n\n";

    // ============================================
    // 13. UPDATE OR CREATE
    // ============================================
    echo "13. Update or Create:\n";
    $article = new Article();
    $result = $article->updateOrCreate(
        ['slug' => 'my-unique-article'],
        ['title' => 'My Unique Article', 'content' => 'Updated content', 'status' => 'published']
    );
    echo $result ? "✓ Updated or created article with slug 'my-unique-article'\n\n" : "✗ Failed\n\n";

    // ============================================
    // 14. CHUNKING FOR LARGE DATASETS
    // ============================================
    echo "14. Chunking (Memory-Efficient Processing):\n";
    $article = new Article();
    $processed = 0;
    $article->all()->chunk(100, function($articles) use (&$processed) {
        // Process 100 articles at a time
        $processed += count($articles);
        echo "  Processed chunk: " . count($articles) . " articles\n";
    });
    echo "✓ Total articles processed: {$processed}\n\n";

    // ============================================
    // 15. RAW QUERIES
    // ============================================
    echo "15. Raw SQL Queries:\n";
    $article = new Article();
    $results = $article->raw(
        "SELECT status, COUNT(*) as count FROM articles WHERE created_at > :date GROUP BY status",
        ['date' => '2024-01-01']
    );
    echo "✓ Executed raw SQL query with parameters\n\n";

    echo "=== Demo Complete ===\n";
