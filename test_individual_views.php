#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;

$filesystem = new Filesystem();
$compiler = new BladeCompiler($filesystem, storage_path('framework/views'));

$views = [
    'artworks/index' => '/var/www/done/resources/views/artworks/index.blade.php',
    'users/show' => '/var/www/done/resources/views/users/show.blade.php',
    'support/index' => '/var/www/done/resources/views/support/index.blade.php'
];

echo "Testing individual view compilation:\n\n";

foreach ($views as $name => $path) {
    echo "Testing $name...\n";
    
    if (!file_exists($path)) {
        echo "  ❌ File not found: $path\n\n";
        continue;
    }
    
    try {
        $content = file_get_contents($path);
        $compiled = $compiler->compileString($content);
        
        // Check for syntax errors in compiled PHP
        $temp = tempnam(sys_get_temp_dir(), 'blade_test');
        file_put_contents($temp, "<?php\n" . $compiled);
        
        $output = shell_exec("php -l $temp 2>&1");
        unlink($temp);
        
        if (strpos($output, 'No syntax errors') !== false) {
            echo "  ✅ Compiles successfully\n";
        } else {
            echo "  ❌ Compilation error:\n";
            echo "     " . trim($output) . "\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}
