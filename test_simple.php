<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Laravel loaded successfully!\n";

// Test if we can access the view() helper at all
try {
    echo "Testing view helper...\n";
    $compiler = app('view');
    echo "View service available!\n";
    
    // Try to compile a super simple view
    $view = $compiler->make('test-dashboard');
    echo "View make() successful!\n";
    
    // The render() call is what's hanging - let's see if we can get more info
    echo "About to render...\n";
    $output = $view->render();
    echo "Render successful!\n";
    echo $output;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
