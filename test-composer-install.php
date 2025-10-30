#!/usr/bin/env php
<?php
/**
 * Test script to verify Composer installation and autoloading
 * 
 * This script tests that:
 * 1. Composer autoloader can be loaded
 * 2. SoftMapper class is available
 * 3. Basic instantiation works
 * 
 * Usage: php test-composer-install.php
 */

echo "Testing Soft-Mapper Composer Installation\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Check if vendor/autoload.php exists
echo "[1/4] Checking for Composer autoloader... ";
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "FAILED\n";
    echo "Error: vendor/autoload.php not found.\n";
    echo "Please run: composer install\n";
    exit(1);
}
echo "OK\n";

// Test 2: Load the autoloader
echo "[2/4] Loading Composer autoloader... ";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "OK\n";
} catch (Exception $e) {
    echo "FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check if SoftMapper class exists
echo "[3/4] Checking if SoftMapper class is available... ";
if (!class_exists('SoftMapper')) {
    echo "FAILED\n";
    echo "Error: SoftMapper class not found in autoloader.\n";
    exit(1);
}
echo "OK\n";

// Test 4: Try to instantiate a test class
echo "[4/4] Testing SoftMapper instantiation... ";
try {
    // Create a simple test class that extends SoftMapper
    class TestModel extends SoftMapper
    {
        public $table_name = "test_table";
        public $columns = [];
        
        // Override constructor to avoid database connection for this test
        public function __construct()
        {
            // Don't call parent::__construct() to avoid DB connection requirement
            $this->table_name = "test_table";
            $this->columns = [];
        }
    }
    
    $test = new TestModel();
    
    if ($test->table_name !== "test_table") {
        echo "FAILED\n";
        echo "Error: Table name not set correctly.\n";
        exit(1);
    }
    
    echo "OK\n";
} catch (Exception $e) {
    echo "FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// All tests passed
echo "\n" . str_repeat("=", 50) . "\n";
echo "✓ All tests passed!\n";
echo "Soft-Mapper is properly installed via Composer.\n";
echo "\nYou can now use it in your project:\n";
echo "  require_once 'vendor/autoload.php';\n";
echo "  class MyModel extends SoftMapper { ... }\n";
exit(0);
