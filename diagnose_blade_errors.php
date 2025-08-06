#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// List of URLs to test
$urls = [
    '/admin',
    '/admin/logs',
    '/admin/users', 
    '/admin/artworks',
    '/admin/languages',
    '/admin/evaluations',
    '/admin/settings',
    '/dashboard',
    '/profile',
    '/artworks',
    '/users/1',
    '/moderation/dashboard',
    '/communities/art-critique-circle',
    '/support',
    '/nft/collection',
    '/communities'
];

echo "🔍 Testing Laravel Routes for Blade Errors...\n\n";

foreach ($urls as $url) {
    echo "Testing: $url\n";
    
    try {
        $request = Illuminate\Http\Request::create($url, 'GET');
        $response = $kernel->handle($request);
        
        echo "   ✅ Status: " . $response->getStatusCode() . "\n";
        
        // Check if it's a view response that might have Blade errors
        if ($response->getStatusCode() >= 500) {
            echo "   ❌ Server Error - checking logs\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        
        // Check if it's a Blade parsing error
        if (strpos($e->getMessage(), 'syntax error') !== false || 
            strpos($e->getMessage(), 'endif') !== false ||
            strpos($e->getMessage(), 'elseif') !== false) {
            echo "   🔥 BLADE SYNTAX ERROR DETECTED!\n";
            echo "   Message: " . $e->getMessage() . "\n";
            
            // Try to extract file info
            $trace = $e->getTraceAsString();
            if (preg_match('/\/storage\/framework\/views\/([^\.]+)\.php/', $trace, $matches)) {
                echo "   Compiled view: " . $matches[1] . ".php\n";
            }
        }
    }
    
    echo "\n";
}

echo "\n🔍 Checking for common Blade issues...\n\n";

// Check specific view files that might have issues
$viewsToCheck = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/admin.blade.php', 
    'resources/views/partials/nav.blade.php',
    'resources/views/partials/footer.blade.php'
];

foreach ($viewsToCheck as $viewPath) {
    if (file_exists($viewPath)) {
        echo "Checking: $viewPath\n";
        $content = file_get_contents($viewPath);
        
        // Check for unclosed directives
        $directives = [
            '@if' => '@endif',
            '@unless' => '@endunless', 
            '@isset' => '@endisset',
            '@empty' => '@endempty',
            '@auth' => '@endauth',
            '@guest' => '@endguest',
            '@section' => '@endsection',
            '@push' => '@endpush',
            '@once' => '@endonce',
            '@php' => '@endphp',
            '@foreach' => '@endforeach',
            '@forelse' => '@endforelse',
            '@for' => '@endfor',
            '@while' => '@endwhile'
        ];
        
        foreach ($directives as $start => $end) {
            $startCount = substr_count($content, $start);
            $endCount = substr_count($content, $end);
            
            if ($startCount !== $endCount) {
                echo "   ❌ Unmatched directives: $start ($startCount) vs $end ($endCount)\n";
            }
        }
        
        echo "   ✅ Directives balanced\n";
    } else {
        echo "   ⚠️  File not found: $viewPath\n";
    }
    echo "\n";
}

echo "Diagnostic complete.\n";
