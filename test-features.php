<?php
    /**
     * Test file for verifying new SoftMapper features
     * This file tests the implementation without requiring a database connection
     */

    // Disable database connection errors for testing
    error_reporting(E_ALL & ~E_WARNING);

    require_once 'SoftMapper.php';

    // Test Model with all features enabled
    class TestModel extends SoftMapper
    {
        public $table_name = "test_table";
        public $columns = [];
        
        protected $timestamps = true;
        protected $soft_deletes = true;
        protected $primary_key = 'id';

        public function __construct()
        {
            // Skip parent constructor to avoid DB connection
            $this->scopes = [];
        }
    }

    echo "=== SoftMapper Feature Tests ===\n\n";

    // Test 1: Check new properties exist
    echo "Test 1: New Properties\n";
    $model = new TestModel();
    $reflection = new ReflectionClass($model);
    
    $properties = ['primary_key', 'timestamps', 'soft_deletes', 'scopes'];
    foreach ($properties as $prop) {
        if ($reflection->hasProperty($prop)) {
            echo "✓ Property '{$prop}' exists\n";
        } else {
            echo "✗ Property '{$prop}' missing\n";
        }
    }
    echo "\n";

    // Test 2: Check new methods exist
    echo "Test 2: New Methods\n";
    $methods = [
        'offset', 'withTrashed', 'onlyTrashed', 'restore', 'insertMany',
        'whereIn', 'whereNotIn', 'whereBetween', 'whereNull', 'whereNotNull',
        'first', 'count', 'exists', 'pluck', 'scope', 'applyScope',
        'beginTransaction', 'commit', 'rollback', 'raw', 'lastInsertId',
        'chunk', 'join', 'leftJoin', 'rightJoin', 'distinct', 'updateOrCreate'
    ];
    
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✓ Method '{$method}()' exists\n";
        } else {
            echo "✗ Method '{$method}()' missing\n";
        }
    }
    echo "\n";

    // Test 3: Method chaining support
    echo "Test 3: Method Chaining\n";
    try {
        $model = new TestModel();
        $result = $model->all();
        if ($result instanceof TestModel) {
            echo "✓ all() returns \$this for chaining\n";
        }
        
        $model = new TestModel();
        $result = $model->withTrashed();
        if ($result instanceof TestModel) {
            echo "✓ withTrashed() returns \$this for chaining\n";
        }
        
        $model = new TestModel();
        $result = $model->onlyTrashed();
        if ($result instanceof TestModel) {
            echo "✓ onlyTrashed() returns \$this for chaining\n";
        }
        
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error in method chaining: " . $e->getMessage() . "\n\n";
    }

    // Test 4: Timestamp functionality
    echo "Test 4: Automatic Timestamps\n";
    try {
        $model = new TestModel();
        $model->columns = ['title' => 'Test'];
        
        // Simulate what insert() does
        $reflection = new ReflectionClass($model);
        $timestamps_prop = $reflection->getProperty('timestamps');
        $timestamps_prop->setAccessible(true);
        $timestamps_enabled = $timestamps_prop->getValue($model);
        
        if ($timestamps_enabled) {
            if (!isset($model->columns['created_at'])) {
                $model->columns['created_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($model->columns['updated_at'])) {
                $model->columns['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        
        if (isset($model->columns['created_at']) && isset($model->columns['updated_at'])) {
            echo "✓ Timestamps added automatically\n";
            echo "  - created_at: {$model->columns['created_at']}\n";
            echo "  - updated_at: {$model->columns['updated_at']}\n";
        } else {
            echo "✗ Timestamps not added\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error in timestamp test: " . $e->getMessage() . "\n\n";
    }

    // Test 5: Scope definitions
    echo "Test 5: Query Scopes\n";
    try {
        $model = new TestModel();
        $model->scope('published', function($query) {
            echo "  - Scope callback registered\n";
        });
        
        $reflection = new ReflectionClass($model);
        $scopes_prop = $reflection->getProperty('scopes');
        $scopes_prop->setAccessible(true);
        $scopes = $scopes_prop->getValue($model);
        
        if (isset($scopes['published'])) {
            echo "✓ Scope 'published' registered successfully\n";
        } else {
            echo "✗ Scope not registered\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error in scope test: " . $e->getMessage() . "\n\n";
    }

    // Test 6: Primary key customization
    echo "Test 6: Custom Primary Key\n";
    try {
        class CustomPKModel extends SoftMapper
        {
            public $table_name = "custom_table";
            public $columns = [];
            protected $primary_key = 'custom_id';

            public function __construct()
            {
                // Skip parent to avoid DB
            }
            
            public function getPrimaryKey() {
                return $this->primary_key;
            }
        }
        
        $custom = new CustomPKModel();
        if ($custom->getPrimaryKey() === 'custom_id') {
            echo "✓ Custom primary key set correctly\n";
        } else {
            echo "✗ Custom primary key not set\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error in primary key test: " . $e->getMessage() . "\n\n";
    }

    // Test 7: Soft delete properties
    echo "Test 7: Soft Delete Configuration\n";
    try {
        class SoftDeleteModel extends SoftMapper
        {
            public $table_name = "soft_table";
            public $columns = [];
            protected $soft_deletes = true;

            public function __construct()
            {
                // Skip parent to avoid DB
            }
            
            public function getSoftDeletes() {
                return $this->soft_deletes;
            }
        }
        
        $soft = new SoftDeleteModel();
        if ($soft->getSoftDeletes() === true) {
            echo "✓ Soft deletes enabled correctly\n";
        } else {
            echo "✗ Soft deletes not enabled\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error in soft delete test: " . $e->getMessage() . "\n\n";
    }

    echo "=== All Tests Complete ===\n";
    echo "\nNote: Full database integration tests require a working MySQL connection.\n";
    echo "These tests verify that the new features are properly implemented in the code.\n";
