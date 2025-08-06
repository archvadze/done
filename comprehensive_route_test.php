<?php

require_once 'vendor/autoload.php';

// Routes to test
$routes = [
    // Basic routes
    '/' => 'Home page',
    '/login' => 'Login page',
    '/register' => 'Register page',
    '/dashboard' => 'Dashboard (requires auth)',
    '/artworks' => 'Artworks listing',
    '/leaderboard' => 'Leaderboard',
    
    // User routes
    '/profile' => 'User profile (requires auth)',
    '/settings' => 'User settings (requires auth)',
    
    // Community routes
    '/community' => 'Community index',
    '/communities' => 'Communities (redirect to /community)',
    
    // Support routes
    '/support' => 'Support index',
    '/support/faq' => 'FAQ index',
    '/support/faq/' => 'FAQ index with trailing slash',
    '/support/contact' => 'Support contact',
    '/support/search' => 'Support search',
    '/support/help' => 'Help articles',
    
    // Admin routes
    '/admin' => 'Admin dashboard (requires admin auth)',
    '/admin/users' => 'Admin users (requires admin auth)',
    '/admin/artworks' => 'Admin artworks (requires admin auth)',
    
    // Moderation routes
    '/moderation' => 'Moderation dashboard (requires auth)',
    
    // Auth routes
    '/auth/facebook' => 'Facebook OAuth redirect',
    '/auth/google' => 'Google OAuth redirect',
    
    // API routes
    '/api/locale/current' => 'Current locale API',
];

$base_url = 'http://done.ddev.site:33000';
$results = [];

echo "Testing all routes...\n\n";

foreach ($routes as $route => $description) {
    $url = $base_url . $route;
    echo "Testing: $url ($description)\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $results[$route] = ['status' => 'ERROR', 'code' => 0, 'error' => $error];
        echo "  ERROR: $error\n";
    } else {
        $results[$route] = ['status' => 'OK', 'code' => $http_code, 'error' => null];
        echo "  HTTP $http_code\n";
        
        // Check for specific issues
        if ($http_code >= 500) {
            echo "  ⚠️  Server error!\n";
        } elseif ($http_code == 404) {
            echo "  ⚠️  Route not found!\n";
        } elseif ($http_code == 302) {
            echo "  ℹ️  Redirect (expected for auth-protected routes)\n";
        } elseif ($http_code == 200) {
            echo "  ✅ OK\n";
        }
    }
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
$errors = 0;
$server_errors = 0;
$not_found = 0;
$ok = 0;
$redirects = 0;

foreach ($results as $route => $result) {
    if ($result['status'] === 'ERROR') {
        $errors++;
        echo "❌ $route - Connection Error: {$result['error']}\n";
    } elseif ($result['code'] >= 500) {
        $server_errors++;
        echo "🔥 $route - Server Error (HTTP {$result['code']})\n";
    } elseif ($result['code'] == 404) {
        $not_found++;
        echo "🔍 $route - Not Found (HTTP 404)\n";
    } elseif ($result['code'] == 302) {
        $redirects++;
        echo "🔄 $route - Redirect (HTTP 302)\n";
    } elseif ($result['code'] == 200) {
        $ok++;
        echo "✅ $route - OK (HTTP 200)\n";
    } else {
        echo "❓ $route - HTTP {$result['code']}\n";
    }
}

echo "\nStats:\n";
echo "✅ OK: $ok\n";
echo "🔄 Redirects: $redirects\n";
echo "🔍 Not Found: $not_found\n";
echo "🔥 Server Errors: $server_errors\n";
echo "❌ Connection Errors: $errors\n";

if ($server_errors > 0 || $not_found > 0) {
    echo "\n⚠️  Issues found that need fixing!\n";
} elseif ($errors > 0) {
    echo "\n⚠️  Connection issues - check if DDEV is running properly\n";
} else {
    echo "\n🎉 All routes working as expected!\n";
}
