<?php
// Simple test to identify the exact error
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== SIMPLE TEST ===\n";

try {
    $request = Illuminate\Http\Request::create('/artworks', 'GET');
    $response = $kernel->handle($request);
    echo "Artworks Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 500) {
        echo "500 ERROR CONTENT:\n";
        $content = $response->getContent();
        
        // Try to extract the actual error message
        if (preg_match('/<h1[^>]*exception_title[^>]*>(.*?)<\/h1>/s', $content, $matches)) {
            echo "Exception Title: " . strip_tags($matches[1]) . "\n";
        }
        
        if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/s', $content, $matches)) {
            echo "Exception Message: " . strip_tags($matches[1]) . "\n";
        }
        
        // Look for stack trace info
        if (preg_match('/in (\/[^:]+):(\d+)/s', $content, $matches)) {
            echo "Error Location: " . $matches[1] . " line " . $matches[2] . "\n";
        }
    }
} catch (Exception $e) {
    echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
