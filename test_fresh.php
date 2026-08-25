<?php

require_once __DIR__ . '/vendor/autoload.php';

echo "Starting fresh test...\n";

$app = require_once __DIR__ . '/bootstrap/app.php';
echo "App loaded successfully!\n";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Kernel created successfully!\n";

$request = Illuminate\Http\Request::capture();
echo "Request captured: " . $request->getUri() . "\n";

// Set a simple URI to test
$request = Illuminate\Http\Request::create('/test', 'GET');
echo "Test request created\n";

try {
    $response = $kernel->handle($request);
    echo "Request handled successfully!\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
} catch (Exception $e) {
    echo "Error handling request: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
