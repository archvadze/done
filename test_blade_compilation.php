<?php

// Simple test to compile app.blade.php and see where it fails
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    $view = view('layouts.app');
    echo "✅ app.blade.php compiles successfully\n";
} catch (\Exception $e) {
    echo "❌ Error compiling app.blade.php:\n";
    echo $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'endif') !== false) {
        echo "\n🔍 This is a Blade directive error!\n";
        
        // Get the compiled file path
        if (preg_match('/storage\/framework\/views\/([^\.]+)\.php/', $e->getMessage(), $matches)) {
            $compiledFile = "storage/framework/views/{$matches[1]}.php";
            if (file_exists($compiledFile)) {
                echo "Compiled file: $compiledFile\n";
                
                // Show the problematic line
                $lines = file($compiledFile);
                $errorLine = 94; // From the error message
                
                if (isset($lines[$errorLine - 1])) {
                    echo "Line $errorLine: " . trim($lines[$errorLine - 1]) . "\n";
                }
            }
        }
    }
}

// Also test the nav partial
try {
    $navView = view('partials.nav');
    echo "✅ nav.blade.php compiles successfully\n";
} catch (\Exception $e) {
    echo "❌ Error compiling nav.blade.php:\n";
    echo $e->getMessage() . "\n";
}

// Test footer partial
try {
    $footerView = view('partials.footer');
    echo "✅ footer.blade.php compiles successfully\n";
} catch (\Exception $e) {
    echo "❌ Error compiling footer.blade.php:\n";
    echo $e->getMessage() . "\n";
}
