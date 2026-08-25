<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    // Test just the nav partial
    $view = view('partials.nav');
    echo "Nav partial compilation successful!\n";
    echo "Output length: " . strlen($view->render()) . " characters\n";
} catch (Exception $e) {
    echo "Error in nav: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

try {
    // Test the layout without nav (create a temporary layout)
    $layoutContent = file_get_contents('resources/views/layouts/app.blade.php');
    $testLayout = str_replace("@include('partials.nav')", "<!-- nav removed -->", $layoutContent);
    file_put_contents('resources/views/layouts/test-app.blade.php', $testLayout);
    
    $view = view('layouts.test-app');
    echo "Layout without nav: successful!\n";
    
    unlink('resources/views/layouts/test-app.blade.php');
} catch (Exception $e) {
    echo "Error in layout: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
