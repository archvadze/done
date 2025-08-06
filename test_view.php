<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== COMPREHENSIVE TESTING ===\n";

// Test 1: Simple route
echo "\n1. Testing home page...\n";
try {
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    echo "Home Status: " . $response->getStatusCode() . " (Content: " . strlen($response->getContent()) . " bytes)\n";
} catch (Exception $e) {
    echo "Home Error: " . $e->getMessage() . "\n";
}

// Test 2: All problematic URLs with error details
echo "\n2. Testing all previously problematic URLs...\n";
$testUrls = ['/artworks', '/users/20', '/support'];

foreach ($testUrls as $url) {
    try {
        $request = Illuminate\Http\Request::create($url, 'GET');
        $response = $kernel->handle($request);
        echo "  $url: " . $response->getStatusCode() . " (" . strlen($response->getContent()) . " bytes)\n";
        
        if ($response->getStatusCode() === 500) {
            $content = $response->getContent();
            // Extract error message from Laravel error page
            if (preg_match('/<h1 class="exception_title">(.*?)<\/h1>/s', $content, $matches)) {
                echo "    Error: " . strip_tags($matches[1]) . "\n";
            } elseif (preg_match('/<title>(.*?)<\/title>/s', $content, $matches)) {
                echo "    Title: " . strip_tags($matches[1]) . "\n";
            }
            // Show first 300 chars of content to understand the error
            echo "    First 300 chars: " . substr(strip_tags($content), 0, 300) . "...\n";
        }
    } catch (Exception $e) {
        echo "  $url: ERROR - " . $e->getMessage() . "\n";
    }
}

// Test 3: Check authentication and user roles
echo "\n3. Testing authentication and user roles...\n";
try {
    $app->make('Illuminate\Database\DatabaseManager')->connection()->getPdo();
    echo "Database: Connected successfully\n";
    
    // Try to count artworks
    $artworkCount = $app->make('App\Models\Artwork')->count();
    echo "Artworks in DB: " . $artworkCount . "\n";
    
    // Check users and their roles
    $users = $app->make('App\Models\User')->select('id', 'name', 'email', 'role', 'status')->limit(10)->get();
    echo "Users in DB: " . $users->count() . "\n";
    foreach ($users as $user) {
        echo "  User {$user->id}: {$user->name} ({$user->email}) - Role: {$user->role} - Status: {$user->status}\n";
    }
    
    // Check if user ID 20 exists (from the problematic URL)
    $user20 = $app->make('App\Models\User')->find(20);
    if ($user20) {
        echo "User ID 20 exists: {$user20->name} - Role: {$user20->role}\n";
    } else {
        echo "User ID 20 does NOT exist\n";
    }
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

// Test 4: Test authentication middleware and session
echo "\n4. Testing authentication middleware...\n";
try {
    // Test if there are any active sessions
    $sessionStore = $app->make('Illuminate\Session\Store');
    echo "Session configured: " . ($sessionStore ? "YES" : "NO") . "\n";
    
    // Test authentication with a request that requires auth
    $request = Illuminate\Http\Request::create('/profile', 'GET');
    $response = $kernel->handle($request);
    echo "Auth-protected route (/profile): " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 302) {
        echo "  Redirected to: " . $response->headers->get('Location') . "\n";
    }
    
} catch (Exception $e) {
    echo "Auth Error: " . $e->getMessage() . "\n";
}

// Test 5: Test layout rendering
echo "\n5. Testing layout rendering...\n";
try {
    $view = view('layouts.app');
    echo "Layout compilation: SUCCESS\n";
} catch (Exception $e) {
    echo "Layout Error: " . $e->getMessage() . "\n";
}
