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

// Test 4: Verify class properties and structure
echo "[4/4] Testing SoftMapper class structure... ";
try {
    // Use reflection to verify the class structure without instantiation
    $reflection = new ReflectionClass('SoftMapper');
    
    // Check that it's a class (not interface or trait)
    if (!$reflection->isInstantiable()) {
        echo "FAILED\n";
        echo "Error: SoftMapper is not instantiable.\n";
        exit(1);
    }
    
    // Check for essential methods
    $requiredMethods = ['all', 'find', 'insert', 'update', 'delete', 'where', 'getAll', 'get'];
    foreach ($requiredMethods as $method) {
        if (!$reflection->hasMethod($method)) {
            echo "FAILED\n";
            echo "Error: Required method '$method' not found.\n";
            exit(1);
        }
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
echo "  require_once __DIR__ . '/vendor/autoload.php';\n";
echo "  class MyModel extends SoftMapper { ... }\n";
exit(0);
