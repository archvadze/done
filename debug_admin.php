<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing admin page...\n";

try {
    $request = Illuminate\Http\Request::create('/admin', 'GET');
    $response = $kernel->handle($request);
    
    echo "Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 500) {
        $content = $response->getContent();
        
        // Look for syntax error
        if (preg_match('/syntax error.*expecting.*elseif.*else.*endif/i', $content)) {
            echo "BLADE SYNTAX ERROR DETECTED!\n";
        }
        
        // Look for specific error details
        if (preg_match('/in ([^:]+):(\d+)/', $content, $matches)) {
            echo "Error in: " . $matches[1] . " line " . $matches[2] . "\n";
        }
        
        // Look for blade compilation error
        if (preg_match('/ParseException.*expecting.*endif/i', $content)) {
            echo "BLADE COMPILATION ERROR!\n";
        }
        
        // Show first part of error for debugging
        echo "First 300 chars of error:\n";
        echo substr(strip_tags($content), 0, 300) . "...\n";
    } elseif ($response->getStatusCode() === 302) {
        echo "Redirected to: " . $response->headers->get('Location') . "\n";
    } else {
        echo "SUCCESS! Content length: " . strlen($response->getContent()) . " bytes\n";
    }
    
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
