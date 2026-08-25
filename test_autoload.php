<?php

// Test just the autoloader
require_once __DIR__ . '/vendor/autoload.php';

echo "Step 1: Autoloader loaded\n";

// Test if we can access Laravel classes
echo "Step 2: Laravel class available: " . class_exists('Illuminate\Foundation\Application') . "\n";

echo "Done - Issue is in bootstrap, not autoloader\n";
