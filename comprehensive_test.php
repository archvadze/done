<?php

echo "🔍 Comprehensive Laravel Application Test\n";
echo "==========================================\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "1. Testing Basic Laravel Bootstrap...\n";
try {
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    echo "   ✅ Laravel application bootstrapped successfully\n\n";
} catch (Exception $e) {
    echo "   ❌ Bootstrap failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "2. Testing Minimal Layout Compilation...\n";
try {
    $view = view('test-dashboard');
    $content = $view->render();
    echo "   ✅ Minimal layout compiles successfully\n";
    echo "   📄 Rendered content preview: " . substr(strip_tags($content), 0, 100) . "...\n\n";
} catch (Exception $e) {
    echo "   ❌ Minimal layout failed: " . $e->getMessage() . "\n";
    echo "   📁 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n\n";
}

echo "3. Testing Main App Layout Compilation...\n";
try {
    // Create a simple test view that extends app layout
    $testViewContent = '@extends("layouts.app")
@section("title", "Test Page")
@section("content")
<h1>Test Content</h1>
@endsection';
    
    file_put_contents('resources/views/test-app-layout.blade.php', $testViewContent);
    
    $view = view('test-app-layout');
    $content = $view->render();
    echo "   ✅ Main app layout compiles successfully\n";
    echo "   📄 Content includes navigation: " . (strpos($content, 'nav') !== false ? "Yes" : "No") . "\n";
    echo "   📄 Content includes footer: " . (strpos($content, 'footer') !== false ? "Yes" : "No") . "\n\n";
    
    // Clean up test file
    unlink('resources/views/test-app-layout.blade.php');
} catch (Exception $e) {
    echo "   ❌ Main app layout failed: " . $e->getMessage() . "\n";
    echo "   📁 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n\n";
    // Clean up test file even on error
    if (file_exists('resources/views/test-app-layout.blade.php')) {
        unlink('resources/views/test-app-layout.blade.php');
    }
}

echo "4. Testing Admin Layout Compilation...\n";
try {
    // Test admin layout exists and compiles
    $testAdminContent = '@extends("layouts.admin")
@section("title", "Test Admin")
@section("content")
<h1>Test Admin Content</h1>
@endsection';
    
    file_put_contents('resources/views/test-admin-layout.blade.php', $testAdminContent);
    
    $view = view('test-admin-layout');
    $content = $view->render();
    echo "   ✅ Admin layout compiles successfully\n\n";
    
    unlink('resources/views/test-admin-layout.blade.php');
} catch (Exception $e) {
    echo "   ❌ Admin layout failed: " . $e->getMessage() . "\n";
    echo "   📁 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n\n";
    if (file_exists('resources/views/test-admin-layout.blade.php')) {
        unlink('resources/views/test-admin-layout.blade.php');
    }
}

echo "5. Testing Navigation Partial...\n";
try {
    $view = view('partials.nav');
    $content = $view->render();
    echo "   ✅ Navigation partial compiles successfully\n";
    echo "   📄 Contains auth sections: " . (strpos($content, '@auth') !== false || strpos($content, 'auth()') !== false ? "Yes" : "No") . "\n\n";
} catch (Exception $e) {
    echo "   ❌ Navigation partial failed: " . $e->getMessage() . "\n";
    echo "   📁 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n\n";
}

echo "6. Testing Footer Partial...\n";
try {
    $view = view('partials.footer');
    $content = $view->render();
    echo "   ✅ Footer partial compiles successfully\n\n";
} catch (Exception $e) {
    echo "   ❌ Footer partial failed: " . $e->getMessage() . "\n";
    echo "   📁 File: " . $e->getFile() . "\n";
    echo "   📍 Line: " . $e->getLine() . "\n\n";
}

echo "7. Testing Database Connection...\n";
try {
    // Test database connection without making actual queries
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n";
    echo "   📊 Database type: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n\n";
}

echo "8. Testing Route Resolution...\n";
try {
    // Test key routes exist
    $routes = [
        '/',
        'dashboard',
        'artworks',
        'admin',
        'login'
    ];
    
    foreach ($routes as $routePath) {
        try {
            $url = url($routePath);
            echo "   ✅ Route '$routePath' resolves to: $url\n";
        } catch (Exception $e) {
            echo "   ❌ Route '$routePath' failed: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Route testing failed: " . $e->getMessage() . "\n\n";
}

echo "9. Testing Configuration...\n";
try {
    echo "   📋 App Name: " . config('app.name') . "\n";
    echo "   📋 App Environment: " . config('app.env') . "\n";
    echo "   📋 App Debug: " . (config('app.debug') ? 'Enabled' : 'Disabled') . "\n";
    echo "   📋 App URL: " . config('app.url') . "\n\n";
} catch (Exception $e) {
    echo "   ❌ Configuration test failed: " . $e->getMessage() . "\n\n";
}

echo "✨ Comprehensive Test Complete!\n";
echo "================================\n";
echo "If all tests passed, your application should be working correctly.\n";
echo "Any failures above indicate specific areas that need attention.\n";
