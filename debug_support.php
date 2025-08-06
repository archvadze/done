<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing /support page...\n";

try {
    $request = Illuminate\Http\Request::create('/support', 'GET');
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 500) {
        $content = $response->getContent();
        
        // Look for route error
        if (preg_match('/Route \[([^\]]+)\] not defined/', $content, $matches)) {
            echo "Missing route: " . $matches[1] . "\n";
        }
        
        // Look for other exceptions
        if (preg_match('/<h1[^>]*>([^<]+)<\/h1>/', $content, $matches)) {
            echo "Exception: " . strip_tags($matches[1]) . "\n";
        }
        
        // Look for file/line info
        if (preg_match('/in ([^:]+):(\d+)/', $content, $matches)) {
            echo "Error in: " . $matches[1] . " line " . $matches[2] . "\n";
        }
    } else {
        echo "SUCCESS!\n";
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
