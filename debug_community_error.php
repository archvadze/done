<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

require_once 'bootstrap/app.php';

try {
    $app = Application::getInstance();
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Create a mock request
    $request = Request::create('/community/art-critique-circle', 'GET');
    
    // Get the kernel and handle the request
    $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
    $response = $kernel->handle($request);
    
    echo "Response Code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 500) {
        $content = $response->getContent();
        
        // Look for the actual error in the response
        if (preg_match('/<h1[^>]*>([^<]+)<\/h1>/', $content, $matches)) {
            echo "Error Title: " . trim($matches[1]) . "\n";
        }
        
        if (preg_match('/<div[^>]*exception_message[^>]*>([^<]+)<\/div>/', $content, $matches)) {
            echo "Exception Message: " . trim($matches[1]) . "\n";
        }
        
        if (preg_match('/Class[^"]*not found/', $content, $matches)) {
            echo "Class not found error detected\n";
        }
        
        if (preg_match('/Call to undefined method/', $content, $matches)) {
            echo "Undefined method error detected\n";
        }
        
        // Try to extract the key error info
        if (preg_match('/<pre[^>]*>([^<]+)<\/pre>/', $content, $matches)) {
            echo "Stack trace excerpt:\n" . substr(trim($matches[1]), 0, 500) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . substr($e->getTraceAsString(), 0, 1000) . "\n";
}
